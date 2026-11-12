/**
 * Format a number as Ethiopian Birr
 * @param {number|string} amount
 * @returns {string} e.g. "ETB 25,000"
 */
export function formatPrice(amount) {
  if (amount === null || amount === undefined) return 'N/A'
  const num = Number(amount)
  return 'ETB ' + num.toLocaleString('en-ET')
}

/**
 * Format a price with /month suffix
 * @param {number|string} amount
 * @returns {string} e.g. "ETB 25,000/mo"
 */
export function formatPricePerMonth(amount) {
  return formatPrice(amount) + '/mo'
}

/**
 * Format a date string to a readable format
 * @param {string|Date} date
 * @param {object} options - Intl.DateTimeFormat options
 * @returns {string}
 */
export function formatDate(date, options = {}) {
  if (!date) return 'N/A'
  const opts = { year: 'numeric', month: 'short', day: 'numeric', ...options }
  return new Date(date).toLocaleDateString('en-US', opts)
}

/**
 * Relative time string
 * @param {string|Date} date
 * @returns {string} e.g. "3 days ago"
 */
export function timeAgo(date) {
  if (!date) return ''
  const seconds = Math.floor((new Date() - new Date(date)) / 1000)
  const intervals = [
    [31536000, 'year'],
    [2592000,  'month'],
    [604800,   'week'],
    [86400,    'day'],
    [3600,     'hour'],
    [60,       'minute'],
  ]
  for (const [secs, label] of intervals) {
    const count = Math.floor(seconds / secs)
    if (count >= 1) return `${count} ${label}${count > 1 ? 's' : ''} ago`
  }
  return 'just now'
}

/**
 * Get the status badge CSS class
 * @param {string} status
 * @returns {string}
 */
export function statusBadgeClass(status) {
  const map = {
    pending:     'badge-pending',
    approved:    'badge-approved',
    rejected:    'badge-rejected',
    rented:      'badge-rented',
    unavailable: 'badge-unavailable',
    accepted:    'badge-accepted',
  }
  return map[status] ?? 'badge bg-gray-100 text-gray-600'
}

/**
 * Truncate a string to max length
 * @param {string} str
 * @param {number} len
 * @returns {string}
 */
export function truncate(str, len = 100) {
  if (!str) return ''
  return str.length > len ? str.slice(0, len) + '…' : str
}

/**
 * Capitalize first letter
 * @param {string} str
 */
export function capitalize(str) {
  if (!str) return ''
  return str.charAt(0).toUpperCase() + str.slice(1).replace(/-/g, ' ')
}
