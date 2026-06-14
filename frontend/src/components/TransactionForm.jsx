import { useState } from 'react'

const initialState = {
  accountNumberFrom: '',
  accountNumberTypeFrom: 'savings',
  accountNumberTo: '',
  accountNumberTypeTo: 'savings',
  amount: '',
  type: 'credit',
  description: '',
  reference: ''
}

function TransactionForm({ onSubmit, onCancel }) {
  const [formData, setFormData] = useState(initialState)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState(null)

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value })
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setLoading(true)
    setError(null)

    try {
      await onSubmit(formData)
      setFormData(initialState)
    } catch (err) {
      setError(err.message)
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="modal-overlay">
      <div className="modal">
        <h2>New Transaction</h2>

        {error && <div className="error-message">{error}</div>}

        <form onSubmit={handleSubmit}>
          <div className="form-grid">
            <div className="form-group">
              <label>Account From</label>
              <input
                type="text"
                name="accountNumberFrom"
                value={formData.accountNumberFrom}
                onChange={handleChange}
                placeholder="Account number"
                required
              />
            </div>

            <div className="form-group">
              <label>Account Type From</label>
              <select
                name="accountNumberTypeFrom"
                value={formData.accountNumberTypeFrom}
                onChange={handleChange}
              >
                <option value="savings">Savings</option>
                <option value="checking">Checking</option>
              </select>
            </div>

            <div className="form-group">
              <label>Account To</label>
              <input
                type="text"
                name="accountNumberTo"
                value={formData.accountNumberTo}
                onChange={handleChange}
                placeholder="Account number"
                required
              />
            </div>

            <div className="form-group">
              <label>Account Type To</label>
              <select
                name="accountNumberTypeTo"
                value={formData.accountNumberTypeTo}
                onChange={handleChange}
              >
                <option value="savings">Savings</option>
                <option value="checking">Checking</option>
              </select>
            </div>

            <div className="form-group">
              <label>Amount</label>
              <input
                type="number"
                name="amount"
                value={formData.amount}
                onChange={handleChange}
                placeholder="0.00"
                step="0.01"
                min="0.01"
                required
              />
            </div>

            <div className="form-group">
              <label>Type</label>
              <select
                name="type"
                value={formData.type}
                onChange={handleChange}
              >
                <option value="credit">Credit</option>
                <option value="debit">Debit</option>
              </select>
            </div>

            <div className="form-group full-width">
              <label>Description</label>
              <input
                type="text"
                name="description"
                value={formData.description}
                onChange={handleChange}
                placeholder="Transaction description"
              />
            </div>

            <div className="form-group full-width">
              <label>Reference</label>
              <input
                type="text"
                name="reference"
                value={formData.reference}
                onChange={handleChange}
                placeholder="Reference number"
              />
            </div>
          </div>

          <div className="form-actions">
            <button type="button" className="btn-secondary" onClick={onCancel}>
              Cancel
            </button>
            <button type="submit" className="btn-primary" disabled={loading}>
              {loading ? 'Creating...' : 'Create Transaction'}
            </button>
          </div>
        </form>
      </div>
    </div>
  )
}

export default TransactionForm