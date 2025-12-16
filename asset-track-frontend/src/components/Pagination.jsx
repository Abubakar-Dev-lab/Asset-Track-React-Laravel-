/**
 * Pagination Component
 * =====================
 *
 * Displays pagination controls for lists.
 *
 * Props:
 *   currentPage: current page number (1-indexed)
 *   totalPages: total number of pages
 *   onPageChange: callback when page changes
 */

function Pagination({ currentPage, totalPages, onPageChange }) {
    // Don't show pagination if only 1 page
    if (totalPages <= 1) return null;

    // Generate page numbers to display
    const getPageNumbers = () => {
        const pages = [];
        const maxVisible = 5;

        let start = Math.max(1, currentPage - Math.floor(maxVisible / 2));
        let end = Math.min(totalPages, start + maxVisible - 1);

        // Adjust start if we're near the end
        if (end - start < maxVisible - 1) {
            start = Math.max(1, end - maxVisible + 1);
        }

        for (let i = start; i <= end; i++) {
            pages.push(i);
        }

        return pages;
    };

    const pages = getPageNumbers();

    return (
        <div className="pagination">
            {/* Previous Button */}
            <button
                className="pagination-btn"
                onClick={() => onPageChange(currentPage - 1)}
                disabled={currentPage === 1}
            >
                ← Prev
            </button>

            {/* First page + ellipsis if needed */}
            {pages[0] > 1 && (
                <>
                    <button
                        className="pagination-btn"
                        onClick={() => onPageChange(1)}
                    >
                        1
                    </button>
                    {pages[0] > 2 && <span className="pagination-info">...</span>}
                </>
            )}

            {/* Page Numbers */}
            {pages.map((page) => (
                <button
                    key={page}
                    className={`pagination-btn ${currentPage === page ? 'active' : ''}`}
                    onClick={() => onPageChange(page)}
                >
                    {page}
                </button>
            ))}

            {/* Last page + ellipsis if needed */}
            {pages[pages.length - 1] < totalPages && (
                <>
                    {pages[pages.length - 1] < totalPages - 1 && (
                        <span className="pagination-info">...</span>
                    )}
                    <button
                        className="pagination-btn"
                        onClick={() => onPageChange(totalPages)}
                    >
                        {totalPages}
                    </button>
                </>
            )}

            {/* Next Button */}
            <button
                className="pagination-btn"
                onClick={() => onPageChange(currentPage + 1)}
                disabled={currentPage === totalPages}
            >
                Next →
            </button>
        </div>
    );
}

export default Pagination;
