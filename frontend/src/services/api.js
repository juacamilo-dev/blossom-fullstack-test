const API_URL = '/transactions'

export const getTransactions = async (filters = {}) => {
  const params = new URLSearchParams()
  
  if (filters.type) params.append('type', filters.type)
  if (filters.date_from) params.append('date_from', filters.date_from)
  if (filters.date_to) params.append('date_to', filters.date_to)
  if (filters.page) params.append('page', filters.page)
  if (filters.limit) params.append('limit', filters.limit)

  const response = await fetch(`${API_URL}?${params.toString()}`)
  if (!response.ok) throw new Error('Error fetching transactions')
  return response.json()
}

export const createTransaction = async (transactionData) => {
  const response = await fetch(API_URL, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(transactionData)
  })
  if (!response.ok) throw new Error('Error creating transaction')
  return response.json()
}

export const deleteTransaction = async (id) => {
  const response = await fetch(`${API_URL}/${id}`, {
    method: 'DELETE'
  })
  if (!response.ok) throw new Error('Error deleting transaction')
  return response.json()
}