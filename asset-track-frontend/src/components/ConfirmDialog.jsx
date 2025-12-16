/**
 * ConfirmDialog Component - Confirmation Modal
 * =============================================
 *
 * Shows a confirmation dialog before destructive actions.
 *
 * Usage:
 *   <ConfirmDialog
 *     isOpen={showConfirm}
 *     title="Delete Asset?"
 *     message="This action cannot be undone."
 *     confirmText="Delete"
 *     confirmVariant="danger"
 *     onConfirm={handleDelete}
 *     onCancel={() => setShowConfirm(false)}
 *   />
 */

function ConfirmDialog({
    isOpen,
    title = 'Confirm Action',
    message = 'Are you sure you want to continue?',
    confirmText = 'Confirm',
    cancelText = 'Cancel',
    confirmVariant = 'danger',  // 'danger', 'primary', 'warning'
    onConfirm,
    onCancel,
    loading = false,
}) {
    if (!isOpen) return null;

    return (
        <div className="confirm-overlay">
            <div className="confirm-dialog">
                <h3>{title}</h3>
                <p>{message}</p>
                <div className="confirm-actions">
                    <button
                        className="btn btn-secondary"
                        onClick={onCancel}
                        disabled={loading}
                    >
                        {cancelText}
                    </button>
                    <button
                        className={`btn btn-${confirmVariant}`}
                        onClick={onConfirm}
                        disabled={loading}
                    >
                        {loading ? 'Please wait...' : confirmText}
                    </button>
                </div>
            </div>
        </div>
    );
}

export default ConfirmDialog;
