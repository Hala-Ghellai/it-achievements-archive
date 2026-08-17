@push('scripts')
<style>
    .confirm-modal {
        position: fixed;
        inset: 0;
        z-index: 1200;
        display: grid;
        place-items: center;
        padding: 20px;
    }

    .confirm-modal[hidden] {
        display: none;
    }

    .confirm-modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(10, 31, 56, 0.55);
        -webkit-backdrop-filter: blur(5px);
        backdrop-filter: blur(5px);
        opacity: 0;
        transition: opacity var(--t-med);
    }

    .confirm-modal-dialog {
        position: relative;
        width: 100%;
        max-width: 400px;
        background: var(--card);
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-lg);
        padding: 30px 26px 26px;
        text-align: center;
        opacity: 0;
        transform: translateY(14px) scale(0.96);
        transition: opacity var(--t-med), transform var(--t-med);
    }

    .confirm-modal.is-open .confirm-modal-backdrop {
        opacity: 1;
    }

    .confirm-modal.is-open .confirm-modal-dialog {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    .confirm-modal-icon {
        width: 62px;
        height: 62px;
        margin: 0 auto 16px;
        border-radius: var(--radius-full);
        background: #fbecec;
        color: var(--danger);
        display: grid;
        place-items: center;
    }

    .confirm-modal-icon svg {
        width: 28px;
        height: 28px;
    }

    .confirm-modal-title {
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--navy-800);
        margin: 0 0 8px;
    }

    .confirm-modal-text {
        color: var(--muted);
        font-size: 0.92rem;
        line-height: 1.7;
        margin: 0 0 22px;
    }

    .confirm-modal-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
    }

    .confirm-btn {
        flex: 1;
        max-width: 160px;
        padding: 11px 18px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
        background: var(--card);
        color: var(--navy-700);
        font-family: inherit;
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        transition: background var(--t-fast), border-color var(--t-fast), color var(--t-fast), transform var(--t-fast);
    }

    .confirm-btn-ghost:hover {
        background: var(--bg-alt);
        border-color: var(--navy-600);
        color: var(--navy-600);
    }

    .confirm-btn-danger {
        background: var(--danger);
        border-color: var(--danger);
        color: #fff;
    }

    .confirm-btn-danger:hover {
        background: #b02e32;
        border-color: #b02e32;
        transform: translateY(-1px);
    }
</style>

<div class="confirm-modal" id="confirm-modal" hidden>
    <div class="confirm-modal-backdrop" data-confirm-close></div>
    <div class="confirm-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="confirm-modal-title">
        <div class="confirm-modal-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M10 11v6M14 11v6"/></svg>
        </div>
        <h3 class="confirm-modal-title" id="confirm-modal-title">تأكيد الحذف</h3>
        <p class="confirm-modal-text" id="confirm-modal-text">سيتم حذف هذا الإنجاز نهائياً مع جميع مرفقاته. لا يمكن التراجع عن هذه العملية.</p>
        <div class="confirm-modal-actions">
            <button type="button" class="confirm-btn confirm-btn-ghost" data-confirm-close>إلغاء</button>
            <button type="button" class="confirm-btn confirm-btn-danger" id="confirm-modal-proceed">حذف نهائي</button>
        </div>
    </div>
</div>

<script>
    (function () {
        'use strict';

        var modal = document.getElementById('confirm-modal');
        var titleEl = document.getElementById('confirm-modal-title');
        var textEl = document.getElementById('confirm-modal-text');
        var proceed = document.getElementById('confirm-modal-proceed');
        var activeForm = null;
        var pendingConfirm = null;

        function open(form) {
            activeForm = form;
            pendingConfirm = null;
            titleEl.textContent = form.dataset.confirmTitle || 'تأكيد الحذف';
            textEl.textContent = form.dataset.confirmText || 'سيتم حذف هذا الإنجاز نهائياً مع جميع مرفقاته. لا يمكن التراجع عن هذه العملية.';
            modal.hidden = false;
            requestAnimationFrame(function () {
                modal.classList.add('is-open');
            });
            document.body.style.overflow = 'hidden';
            proceed.focus();
        }

        function close() {
            modal.classList.remove('is-open');
            setTimeout(function () {
                modal.hidden = true;
                document.body.style.overflow = '';
            }, 260);
            activeForm = null;
            pendingConfirm = null;
        }

        proceed.addEventListener('click', function () {
            if (pendingConfirm) {
                var cb = pendingConfirm;
                pendingConfirm = null;
                close();
                cb();
                return;
            }
            if (activeForm) {
                activeForm.submit();
            }
        });

        window.ConfirmDialog = {
            open: function (opts) {
                activeForm = null;
                pendingConfirm = opts.onConfirm || null;
                titleEl.textContent = opts.title || 'تأكيد الحذف';
                textEl.textContent = opts.text || 'سيتم حذف هذا العنصر نهائياً. لا يمكن التراجع عن هذه العملية.';
                modal.hidden = false;
                requestAnimationFrame(function () {
                    modal.classList.add('is-open');
                });
                document.body.style.overflow = 'hidden';
                proceed.focus();
            }
        };

        modal.querySelectorAll('[data-confirm-close]').forEach(function (el) {
            el.addEventListener('click', close);
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !modal.hidden) {
                close();
            }
        });

        document.querySelectorAll('form.confirm-delete-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                open(form);
            });
        });
    })();
</script>
@endpush