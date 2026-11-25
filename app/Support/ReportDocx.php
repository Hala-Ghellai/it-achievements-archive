<?php

namespace App\Support;

use RuntimeException;
use ZipArchive;

/**
 * Builds the periodic report as a real, editable Word document (.docx),
 * with the same official layout as the printable page. No extra packages:
 * the file is assembled from WordprocessingML with PHP's ZipArchive.
 */
class ReportDocx
{
    private const FONT = 'AL-Mohanad';

    private const GOLD = '8A6A36';

    private const SOFT = '5B6068';

    private const LINE = 'D6D0C4';

    private const HEAD_BG = 'F4EFE6';

    /** Usable page width in twips (A4 21cm minus 2 × 1.8cm margins). */
    private const PAGE_WIDTH = 9866;

    /** @var array<int, array{target: string, ext: string}> */
    private array $images = [];

    private int $nextRelId = 10;

    /** @param array<string, mixed> $report Data produced by ReportController::buildReport(). */
    public function __construct(private array $report)
    {
    }

    /** Write the .docx to a temporary file and return its path. */
    public function save(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'report').'.docx';
        $body = $this->body();

        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Unable to create the Word file.');
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypes());
        $zip->addFromString('_rels/.rels', $this->rootRels());
        $zip->addFromString('docProps/core.xml', $this->coreProps());
        $zip->addFromString('docProps/app.xml', $this->appProps());
        $zip->addFromString('word/document.xml', $this->document($body));
        $zip->addFromString('word/styles.xml', $this->styles());
        $zip->addFromString('word/settings.xml', $this->settings());
        $zip->addFromString('word/footer1.xml', $this->footer());
        $zip->addFromString('word/_rels/document.xml.rels', $this->documentRels());

        foreach ($this->images as $id => $image) {
            $zip->addFile($image['source'], 'word/media/image'.$id.'.'.$image['ext']);
        }

        $zip->close();

        return $path;
    }

    // ------------------------------------------------------------------
    // Report content
    // ------------------------------------------------------------------

    private function body(): string
    {
        $r = $this->report;
        $dept = $r['selectedDepartment'];
        $sections = $r['sections'];
        $total = (int) $r['total'];
        $withTeam = (bool) $r['withTeam'];
        $deptName = config('site.department_name');
        $org = config('site.organization_name');

        $xml = $this->letterhead();

        // Title
        $xml .= $this->p($this->run($r['reportTypeName'].' لأعمال '.($dept->name ?? $deptName), ['b' => true, 'sz' => 40]), ['jc' => 'center', 'before' => 360, 'after' => 60]);
        $xml .= $this->p($this->run($r['periodName'], ['b' => true, 'sz' => 30, 'color' => self::GOLD]), ['jc' => 'center', 'after' => 60]);
        $xml .= $this->p($this->run('الفترة من '.$this->date($r['start']).' إلى '.$this->date($r['end']), ['sz' => 24, 'color' => self::SOFT]), ['jc' => 'center', 'after' => 240]);

        // 1. Executive summary
        $xml .= $this->heading('1', 'الملخص التنفيذي');
        $scope = $dept ? 'الخاصة بـ'.$dept->name : 'لجميع أقسام الإدارة';
        $word = ($total >= 3 && $total <= 10) ? 'أعمال' : 'عملاً';
        $xml .= $this->p($this->run("يستعرض هذا التقرير الأعمال المسجلة في أرشيف {$deptName} {$scope} خلال {$r['periodName']}، وقد بلغ إجمالي الأعمال المنفذة خلال هذه الفترة ({$total}) {$word}.", ['color' => self::SOFT]), ['jc' => 'both', 'after' => 160]);

        $active = 0;
        $team = [];
        foreach ($sections as $section) {
            if (count($section['achievements']) > 0) {
                $active++;
            }
            foreach ($section['achievements'] as $a) {
                foreach ($a->employees as $e) {
                    $team[$e->id] = true;
                }
            }
        }

        $figures = [
            [(string) $total, 'إجمالي الأعمال'],
            [$dept ? '1' : $active.' / '.count($sections), $dept ? 'القسم المشمول' : 'الأقسام ذات الأعمال'],
        ];
        if ($withTeam) {
            $figures[] = [(string) count($team), 'الموظفون المساهمون'];
        }
        $xml .= $this->figuresTable($figures);

        $n = 2;

        // 2. Distribution (all departments only)
        if (! $dept) {
            $xml .= $this->heading((string) $n++, 'توزيع الأعمال حسب الأقسام');
            $rows = [];
            $i = 1;
            foreach ($sections as $section) {
                $count = count($section['achievements']);
                $share = $total > 0 ? round($count / $total * 100) : 0;
                $rows[] = [(string) $i++, $section['department']->name, (string) $count, $share.'%'];
            }
            $xml .= $this->table(
                ['م', 'القسم', 'عدد الأعمال', 'النسبة'],
                [700, 5566, 1800, 1800],
                $rows,
                ['', 'الإجمالي', (string) $total, $total > 0 ? '100%' : '0%'],
                [0, 2, 3]
            );
        }

        // 3. Details per department
        $xml .= $this->heading((string) $n, 'تفاصيل الأعمال');

        foreach ($sections as $section) {
            $department = $section['department'];
            $items = $section['achievements'];
            $color = ltrim((string) ($department->color ?: '#'.self::GOLD), '#');

            $xml .= $this->p(
                $this->run($department->name, ['b' => true, 'sz' => 28]).
                $this->run('   —   '.\App\Models\Department::countLabel(count($items)), ['sz' => 22, 'color' => self::SOFT]),
                ['before' => 280, 'after' => 100, 'shd' => self::HEAD_BG, 'borderStart' => $color, 'keepNext' => true]
            );

            if (count($items) === 0) {
                $xml .= $this->p($this->run('لا توجد أعمال مسجلة لهذا القسم خلال الفترة المحددة.', ['color' => self::SOFT, 'sz' => 22]), ['jc' => 'center', 'after' => 120]);

                continue;
            }

            $headers = ['م', 'العمل', 'التاريخ'];
            $widths = $withTeam ? [600, 5266, 1600, 2400] : [600, 7366, 1900];
            if ($withTeam) {
                $headers[] = 'فريق العمل';
            }

            $rowsXml = '';
            $i = 1;
            foreach ($items as $a) {
                $cells = [
                    $this->cell($widths[0], $this->p($this->run((string) $i++), ['jc' => 'center'])),
                    $this->cell($widths[1], $this->workCell($a)),
                    $this->cell($widths[2], $this->p($this->run($this->date($a->achievement_at), ['sz' => 22]))),
                ];
                if ($withTeam) {
                    $names = [];
                    foreach ($a->employees as $e) {
                        $names[] = $e->full_name;
                    }
                    $cells[] = $this->cell($widths[3], $this->p($this->run($names ? implode('، ', $names) : '—', ['sz' => 22])));
                }
                $rowsXml .= '<w:tr><w:trPr><w:cantSplit/></w:trPr>'.implode('', $cells).'</w:tr>';
            }

            $xml .= $this->tableXml($widths, $this->headerRow($headers, $widths).$rowsXml);
        }

        // Signatures
        $xml .= $this->p('', ['before' => 600]);
        $xml .= $this->signatures();

        // Closing line
        $xml .= $this->p(
            $this->run("صدر هذا التقرير آلياً من أرشيف أعمال {$deptName} — {$org}", ['sz' => 18, 'color' => self::SOFT]),
            ['jc' => 'center', 'before' => 480, 'borderTop' => 'EBE6DC']
        );

        return $xml;
    }

    private function letterhead(): string
    {
        $r = $this->report;
        $orgLines = [
            [config('site.report.country'), null],
            [config('site.report.ministry'), null],
            [config('site.organization_name'), null],
            [config('site.department_name'), self::GOLD],
        ];

        $org = '';
        foreach ($orgLines as [$text, $color]) {
            $org .= $this->p($this->run((string) $text, ['b' => true, 'sz' => 26, 'color' => $color]), ['jc' => 'center', 'line' => 260]);
        }

        $logoPath = public_path('images/mashlaha-logo.jpg');
        $logo = is_file($logoPath)
            ? $this->p($this->image($logoPath, 1500, 1500), ['jc' => 'center'])
            : $this->p('');

        // Labels and values in a small borderless two-column table, so the lines align.
        $metaLines = [
            ['رقم التقرير:', $r['reportNumber']],
            ['تاريخ الإصدار:', $this->date($r['issuedAt'])],
            ['النطاق:', $r['selectedDepartment']->name ?? 'جميع الأقسام'],
        ];

        $mw = [1550, 2350];
        $metaRows = '';
        foreach ($metaLines as [$label, $value]) {
            $metaRows .= '<w:tr>'.
                $this->cell($mw[0], $this->p($this->run($label, ['sz' => 22, 'color' => self::SOFT]), ['after' => 40]), ['noBorders' => true]).
                $this->cell($mw[1], $this->p($this->run((string) $value, ['sz' => 22, 'b' => true]), ['after' => 40]), ['noBorders' => true]).
                '</w:tr>';
        }
        $meta = $this->tableXml($mw, $metaRows, false).$this->p('', ['after' => 0, 'line' => 120]);

        $w = [4183, 1500, 4183];
        $row = '<w:tr>'.
            $this->cell($w[0], $org, ['vAlign' => 'center', 'noBorders' => true]).
            $this->cell($w[1], $logo, ['vAlign' => 'center', 'noBorders' => true]).
            $this->cell($w[2], $meta, ['vAlign' => 'center', 'noBorders' => true]).
            '</w:tr>';

        return $this->tableXml($w, $row, false).
            // Double gold rule under the letterhead
            $this->p('', ['borderBottom' => self::GOLD, 'borderBottomStyle' => 'thinThickSmallGap', 'borderBottomSize' => 18, 'after' => 0]);
    }

    private function workCell(object $a): string
    {
        $xml = $this->p($this->run($a->title, ['b' => true]), ['after' => 40]);

        foreach (preg_split('/\R/u', trim((string) $a->description)) as $line) {
            $xml .= $this->p($this->run($line, ['sz' => 22, 'color' => self::SOFT]), ['after' => 0]);
        }

        if ($this->report['withImages']) {
            $runs = '';
            $count = 0;
            foreach ($a->media as $media) {
                if ($count >= 3) {
                    break;
                }
                $file = storage_path('app/public/'.$media->url);
                if (is_file($file) && @getimagesize($file)) {
                    $runs .= $this->image($file, 1650, null).$this->run(' ');
                    $count++;
                }
            }
            if ($runs !== '') {
                $xml .= $this->p($runs, ['before' => 80]);
            }
        }

        return $xml;
    }

    private function signatures(): string
    {
        $left = $this->p($this->run((string) config('site.report.prepared_by_title'), ['b' => true]), ['jc' => 'center', 'after' => 700]).
            $this->p($this->run('............................................', ['color' => self::SOFT]), ['jc' => 'center', 'after' => 0]).
            $this->p($this->run((string) config('site.report.prepared_by_name'), ['sz' => 22, 'color' => self::SOFT]), ['jc' => 'center']);

        $right = $this->p($this->run((string) config('site.report.approved_by_title'), ['b' => true]), ['jc' => 'center', 'after' => 700]).
            $this->p($this->run('............................................', ['color' => self::SOFT]), ['jc' => 'center', 'after' => 0]).
            $this->p($this->run('الاسم والتوقيع', ['sz' => 22, 'color' => self::SOFT]), ['jc' => 'center']);

        $w = [4933, 4933];

        return $this->tableXml($w, '<w:tr><w:trPr><w:cantSplit/></w:trPr>'.
            $this->cell($w[0], $left, ['noBorders' => true]).
            $this->cell($w[1], $right, ['noBorders' => true]).'</w:tr>', false);
    }

    private function heading(string $number, string $text): string
    {
        return $this->p(
            $this->run($number.'.  ', ['b' => true, 'sz' => 30, 'color' => self::GOLD]).$this->run($text, ['b' => true, 'sz' => 30]),
            ['before' => 360, 'after' => 140, 'borderBottom' => self::LINE, 'keepNext' => true]
        );
    }

    /** @param array<int, array{0: string, 1: string}> $figures */
    private function figuresTable(array $figures): string
    {
        $w = array_fill(0, count($figures), intdiv(self::PAGE_WIDTH, count($figures)));
        $cells = '';
        foreach ($figures as $i => [$value, $label]) {
            $cells .= $this->cell($w[$i],
                $this->p($this->run($value, ['b' => true, 'sz' => 40, 'ltr' => true]), ['jc' => 'center', 'after' => 0]).
                $this->p($this->run($label, ['sz' => 22, 'color' => self::SOFT]), ['jc' => 'center'])
            );
        }

        return $this->tableXml($w, '<w:tr>'.$cells.'</w:tr>');
    }

    /**
     * @param array<int, string> $headers
     * @param array<int, int> $widths
     * @param array<int, array<int, string>> $rows
     * @param array<int, string>|null $footer
     * @param array<int, int> $centered
     */
    private function table(array $headers, array $widths, array $rows, ?array $footer, array $centered = []): string
    {
        $xml = $this->headerRow($headers, $widths);

        foreach ($rows as $row) {
            $xml .= '<w:tr><w:trPr><w:cantSplit/></w:trPr>';
            foreach ($row as $i => $value) {
                $xml .= $this->cell($widths[$i], $this->p($this->run($value), ['jc' => in_array($i, $centered, true) ? 'center' : null]));
            }
            $xml .= '</w:tr>';
        }

        if ($footer) {
            $xml .= '<w:tr>';
            foreach ($footer as $i => $value) {
                $xml .= $this->cell($widths[$i], $this->p($this->run($value, ['b' => true]), ['jc' => in_array($i, $centered, true) ? 'center' : null]), ['shd' => self::HEAD_BG]);
            }
            $xml .= '</w:tr>';
        }

        return $this->tableXml($widths, $xml);
    }

    /** @param array<int, string> $headers @param array<int, int> $widths */
    private function headerRow(array $headers, array $widths): string
    {
        $xml = '<w:tr><w:trPr><w:tblHeader/><w:cantSplit/></w:trPr>';
        foreach ($headers as $i => $h) {
            $xml .= $this->cell($widths[$i], $this->p($this->run($h, ['b' => true, 'sz' => 22]), ['jc' => $i === 0 ? 'center' : null]), ['shd' => self::HEAD_BG]);
        }

        return $xml.'</w:tr>';
    }

    // ------------------------------------------------------------------
    // WordprocessingML helpers
    // ------------------------------------------------------------------

    /** @param array<int, int> $widths */
    private function tableXml(array $widths, string $rows, bool $borders = true, string $align = 'center'): string
    {
        $grid = '';
        foreach ($widths as $w) {
            $grid .= '<w:gridCol w:w="'.$w.'"/>';
        }

        $b = $borders
            ? '<w:tblBorders>'.implode('', array_map(fn ($side) => '<w:'.$side.' w:val="single" w:sz="4" w:space="0" w:color="'.self::LINE.'"/>', ['top', 'left', 'bottom', 'right', 'insideH', 'insideV'])).'</w:tblBorders>'
            : '<w:tblBorders>'.implode('', array_map(fn ($side) => '<w:'.$side.' w:val="nil"/>', ['top', 'left', 'bottom', 'right', 'insideH', 'insideV'])).'</w:tblBorders>';

        return '<w:tbl><w:tblPr><w:bidiVisual/><w:tblW w:w="'.array_sum($widths).'" w:type="dxa"/><w:jc w:val="'.$align.'"/>'.$b.
            '<w:tblLayout w:type="fixed"/><w:tblCellMar><w:top w:w="70" w:type="dxa"/><w:left w:w="110" w:type="dxa"/><w:bottom w:w="70" w:type="dxa"/><w:right w:w="110" w:type="dxa"/></w:tblCellMar></w:tblPr>'.
            '<w:tblGrid>'.$grid.'</w:tblGrid>'.$rows.'</w:tbl>';
    }

    /** @param array<string, mixed> $o */
    private function cell(int $width, string $content, array $o = []): string
    {
        $pr = '<w:tcW w:w="'.$width.'" w:type="dxa"/>';
        if (! empty($o['noBorders'])) {
            $pr .= '<w:tcBorders><w:top w:val="nil"/><w:left w:val="nil"/><w:bottom w:val="nil"/><w:right w:val="nil"/></w:tcBorders>';
        }
        if (! empty($o['shd'])) {
            $pr .= '<w:shd w:val="clear" w:color="auto" w:fill="'.$o['shd'].'"/>';
        }
        if (! empty($o['vAlign'])) {
            $pr .= '<w:vAlign w:val="'.$o['vAlign'].'"/>';
        }

        return '<w:tc><w:tcPr>'.$pr.'</w:tcPr>'.($content !== '' ? $content : '<w:p/>').'</w:tc>';
    }

    /** @param array<string, mixed> $o */
    private function p(string $runs, array $o = []): string
    {
        $pr = '';
        if (! empty($o['keepNext'])) {
            $pr .= '<w:keepNext/>';
        }

        $borders = '';
        if (! empty($o['borderTop'])) {
            $borders .= '<w:top w:val="single" w:sz="4" w:space="6" w:color="'.$o['borderTop'].'"/>';
        }
        if (! empty($o['borderBottom'])) {
            $borders .= '<w:bottom w:val="'.($o['borderBottomStyle'] ?? 'single').'" w:sz="'.($o['borderBottomSize'] ?? 6).'" w:space="4" w:color="'.$o['borderBottom'].'"/>';
        }
        if (! empty($o['borderStart'])) {
            // In a right-to-left paragraph the "right" border sits at the start of the line.
            $borders .= '<w:right w:val="single" w:sz="36" w:space="8" w:color="'.$o['borderStart'].'"/>';
        }
        if ($borders !== '') {
            $pr .= '<w:pBdr>'.$borders.'</w:pBdr>';
        }
        if (! empty($o['shd'])) {
            $pr .= '<w:shd w:val="clear" w:color="auto" w:fill="'.$o['shd'].'"/>';
        }

        $pr .= '<w:bidi/>';
        $pr .= '<w:spacing w:before="'.($o['before'] ?? 0).'" w:after="'.($o['after'] ?? 80).'"'.
            (isset($o['line']) ? ' w:line="'.$o['line'].'" w:lineRule="auto"' : '').'/>';

        if (! empty($o['jc'])) {
            $pr .= '<w:jc w:val="'.$o['jc'].'"/>';
        }

        return '<w:p><w:pPr>'.$pr.'</w:pPr>'.$runs.'</w:p>';
    }

    /** @param array<string, mixed> $o */
    private function run(string $text, array $o = []): string
    {
        $sz = (int) ($o['sz'] ?? 24);
        $pr = '<w:rFonts w:ascii="'.self::FONT.'" w:hAnsi="'.self::FONT.'" w:cs="'.self::FONT.'"/>';
        if (! empty($o['b'])) {
            $pr .= '<w:b/><w:bCs/>';
        }
        if (! empty($o['color'])) {
            $pr .= '<w:color w:val="'.$o['color'].'"/>';
        }
        $pr .= '<w:sz w:val="'.$sz.'"/><w:szCs w:val="'.$sz.'"/>';
        if (empty($o['ltr'])) {
            $pr .= '<w:rtl/>';
        }

        return '<w:r><w:rPr>'.$pr.'</w:rPr><w:t xml:space="preserve">'.htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8').'</w:t></w:r>';
    }

    /**
     * An inline picture. Width/height are in twips; when one is null it is
     * derived from the picture's aspect ratio.
     */
    private function image(string $file, ?int $widthTw, ?int $heightTw): string
    {
        [$pxW, $pxH, $type] = getimagesize($file);
        $ext = $type === IMAGETYPE_PNG ? 'png' : ($type === IMAGETYPE_GIF ? 'gif' : 'jpeg');

        if ($heightTw === null) {
            $heightTw = (int) round($widthTw * $pxH / max(1, $pxW));
        } elseif ($widthTw === null) {
            $widthTw = (int) round($heightTw * $pxW / max(1, $pxH));
        }

        $id = $this->nextRelId++;
        $this->images[$id] = ['source' => $file, 'ext' => $ext];

        $cx = $widthTw * 635; // twips → EMU
        $cy = $heightTw * 635;

        return '<w:r><w:drawing><wp:inline distT="0" distB="0" distL="0" distR="0">'.
            '<wp:extent cx="'.$cx.'" cy="'.$cy.'"/><wp:docPr id="'.$id.'" name="Picture '.$id.'"/>'.
            '<wp:cNvGraphicFramePr><a:graphicFrameLocks noChangeAspect="1"/></wp:cNvGraphicFramePr>'.
            '<a:graphic><a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture">'.
            '<pic:pic><pic:nvPicPr><pic:cNvPr id="'.$id.'" name="image'.$id.'.'.$ext.'"/><pic:cNvPicPr/></pic:nvPicPr>'.
            '<pic:blipFill><a:blip r:embed="rId'.$id.'"/><a:stretch><a:fillRect/></a:stretch></pic:blipFill>'.
            '<pic:spPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="'.$cx.'" cy="'.$cy.'"/></a:xfrm><a:prstGeom prst="rect"><a:avLst/></a:prstGeom></pic:spPr>'.
            '</pic:pic></a:graphicData></a:graphic></wp:inline></w:drawing></w:r>';
    }

    private function date(mixed $date): string
    {
        return $date->translatedFormat('j F Y');
    }

    // ------------------------------------------------------------------
    // Package parts
    // ------------------------------------------------------------------

    private function document(string $body): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
            '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" '.
            'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" '.
            'xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing" '.
            'xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" '.
            'xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture">'.
            '<w:body>'.$body.
            '<w:sectPr><w:footerReference w:type="default" r:id="rIdFooter"/>'.
            '<w:pgSz w:w="11906" w:h="16838"/>'.
            '<w:pgMar w:top="1000" w:right="1020" w:bottom="1100" w:left="1020" w:header="567" w:footer="567" w:gutter="0"/>'.
            '<w:bidi/></w:sectPr></w:body></w:document>';
    }

    private function footer(): string
    {
        $r = fn (string $t, bool $ltr = false) => $this->run($t, ['sz' => 18, 'color' => '777777', 'ltr' => $ltr]);
        $field = fn (string $code) => '<w:r><w:rPr><w:sz w:val="18"/><w:szCs w:val="18"/><w:color w:val="777777"/></w:rPr><w:fldChar w:fldCharType="begin"/></w:r>'.
            '<w:r><w:rPr><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:instrText xml:space="preserve"> '.$code.' </w:instrText></w:r>'.
            '<w:r><w:rPr><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:fldChar w:fldCharType="separate"/></w:r>'.
            '<w:r><w:rPr><w:sz w:val="18"/><w:szCs w:val="18"/><w:color w:val="777777"/></w:rPr><w:t>1</w:t></w:r>'.
            '<w:r><w:rPr><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:fldChar w:fldCharType="end"/></w:r>';

        $para = $this->p(
            $r('صفحة ').$field('PAGE').$r(' من ').$field('NUMPAGES').$r('    |    ').$r($this->report['reportNumber']),
            ['jc' => 'center', 'after' => 0]
        );

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
            '<w:ftr xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'.
            $para.'</w:ftr>';
    }

    private function styles(): string
    {
        $f = self::FONT;

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
            '<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'.
            '<w:docDefaults><w:rPrDefault><w:rPr><w:rFonts w:ascii="'.$f.'" w:hAnsi="'.$f.'" w:eastAsia="'.$f.'" w:cs="'.$f.'"/>'.
            '<w:sz w:val="24"/><w:szCs w:val="24"/><w:lang w:val="ar-LY" w:bidi="ar-LY"/></w:rPr></w:rPrDefault>'.
            '<w:pPrDefault><w:pPr><w:bidi/><w:spacing w:after="80" w:line="264" w:lineRule="auto"/></w:pPr></w:pPrDefault></w:docDefaults>'.
            '<w:style w:type="paragraph" w:default="1" w:styleId="Normal"><w:name w:val="Normal"/><w:qFormat/><w:pPr><w:bidi/></w:pPr><w:rPr><w:rtl/></w:rPr></w:style>'.
            '<w:style w:type="table" w:default="1" w:styleId="TableNormal"><w:name w:val="Normal Table"/><w:tblPr><w:bidiVisual/><w:tblCellMar><w:left w:w="108" w:type="dxa"/><w:right w:w="108" w:type="dxa"/></w:tblCellMar></w:tblPr></w:style>'.
            '</w:styles>';
    }

    private function settings(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
            '<w:settings xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'.
            '<w:updateFields w:val="true"/><w:defaultTabStop w:val="720"/><w:themeFontLang w:val="en-US" w:bidi="ar-LY"/>'.
            '<w:compat><w:compatSetting w:name="compatibilityMode" w:uri="http://schemas.microsoft.com/office/word" w:val="15"/></w:compat>'.
            '</w:settings>';
    }

    private function contentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
            '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'.
            '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'.
            '<Default Extension="xml" ContentType="application/xml"/>'.
            '<Default Extension="jpeg" ContentType="image/jpeg"/>'.
            '<Default Extension="png" ContentType="image/png"/>'.
            '<Default Extension="gif" ContentType="image/gif"/>'.
            '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'.
            '<Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>'.
            '<Override PartName="/word/settings.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.settings+xml"/>'.
            '<Override PartName="/word/footer1.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.footer+xml"/>'.
            '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'.
            '<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'.
            '</Types>';
    }

    private function rootRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'.
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'.
            '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'.
            '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'.
            '</Relationships>';
    }

    private function documentRels(): string
    {
        $rels = '<Relationship Id="rIdStyles" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'.
            '<Relationship Id="rIdSettings" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/settings" Target="settings.xml"/>'.
            '<Relationship Id="rIdFooter" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/footer" Target="footer1.xml"/>';

        foreach ($this->images as $id => $image) {
            $rels .= '<Relationship Id="rId'.$id.'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/image'.$id.'.'.$image['ext'].'"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'.$rels.'</Relationships>';
    }

    private function coreProps(): string
    {
        $title = htmlspecialchars($this->report['reportTypeName'].' — '.$this->report['periodName'], ENT_XML1, 'UTF-8');
        $now = gmdate('Y-m-d\TH:i:s\Z');

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
            '<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'.
            '<dc:title>'.$title.'</dc:title><dc:creator>'.htmlspecialchars((string) config('site.department_name'), ENT_XML1, 'UTF-8').'</dc:creator>'.
            '<dcterms:created xsi:type="dcterms:W3CDTF">'.$now.'</dcterms:created><dcterms:modified xsi:type="dcterms:W3CDTF">'.$now.'</dcterms:modified>'.
            '</cp:coreProperties>';
    }

    private function appProps(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
            '<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties"><Application>IT Achievements Archive</Application></Properties>';
    }
}
