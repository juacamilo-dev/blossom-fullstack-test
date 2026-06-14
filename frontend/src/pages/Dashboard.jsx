import { useState, useEffect } from 'react'
import TransactionTable from '../components/TransactionTable'
import TransactionForm from '../components/TransactionForm'
import Filters from '../components/Filters'
import { getTransactions, createTransaction, deleteTransaction } from '../services/api'

const initialFilters = { type: '', date_from: '', date_to: '', page: 1, limit: 10 }

function Dashboard() {
  const [transactions, setTransactions] = useState([])
  const [pagination, setPagination] = useState({})
  const [filters, setFilters] = useState(initialFilters)
  const [showForm, setShowForm] = useState(false)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState(null)

  const fetchTransactions = async (currentFilters) => {
    setLoading(true)
    setError(null)
    try {
      const result = await getTransactions(currentFilters)
      setTransactions(result.data)
      setPagination(result.pagination)
    } catch (err) {
      setError('Error loading transactions. Is the backend running?')
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    fetchTransactions(filters)
  }, [])

  const handleFilterChange = (key, value) => {
    setFilters({ ...filters, [key]: value })
  }

  const handleApplyFilters = () => {
    const newFilters = { ...filters, page: 1 }
    setFilters(newFilters)
    fetchTransactions(newFilters)
  }

  const handleClearFilters = () => {
    setFilters(initialFilters)
    fetchTransactions(initialFilters)
  }

  const handleCreate = async (formData) => {
    await createTransaction(formData)
    setShowForm(false)
    fetchTransactions(filters)
  }

  const handleDelete = async (id) => {
    if (!window.confirm('Are you sure you want to delete this transaction?')) return
    await deleteTransaction(id)
    fetchTransactions(filters)
  }

  const handlePageChange = (newPage) => {
    const newFilters = { ...filters, page: newPage }
    setFilters(newFilters)
    fetchTransactions(newFilters)
  }

  return (
    <div className="dashboard">
      <header className="dashboard-header">
        <div>
          <h1>Transaction Management</h1>
          <p>Blossom Financial System</p>
        </div>
        <button className="btn-primary" onClick={() => setShowForm(true)}>
          + New Transaction
        </button>
      </header>

      {error && <div className="error-banner">{error}</div>}

      <div className="dashboard-stats">
        <div className="stat-card">
          <span>Total Transactions</span>
          <strong>{pagination.total || 0}</strong>
        </div>
        <div className="stat-card">
          <span>Page</span>
          <strong>{pagination.page || 1} of {pagination.total_pages || 1}</strong>
        </div>
      </div>

      <Filters
        filters={filters}
        onFilterChange={handleFilterChange}
        onApply={handleApplyFilters}
        onClear={handleClearFilters}
      />

      <TransactionTable
        transactions={transactions}
        onDelete={handleDelete}
        loading={loading}
      />

      {pagination.total_pages > 1 && (
        <div className="pagination">
          <button
            className="btn-secondary"
            disabled={filters.page <= 1}
            onClick={() => handlePageChange(filters.page - 1)}
          >
            Previous
          </button>
          <span>Page {filters.page} of {pagination.total_pages}</span>
          <button
            className="btn-secondary"
            disabled={filters.page >= pagination.total_pages}
            onClick={() => handlePageChange(filters.page + 1)}
          >
            Next
          </button>
        </div>
      )}

      {showForm && (
        <TransactionForm
          onSubmit={handleCreate}
          onCancel={() => setShowForm(false)}
        />
      )}
    </div>
  )
}

export default Dashboard