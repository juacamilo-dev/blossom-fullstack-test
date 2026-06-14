function TransactionTable({ transactions, onDelete, loading }) {
    if (loading) return <div className="loading">Loading transactions...</div>
  
    if (transactions.length === 0) {
      return <div className="empty">No transactions found.</div>
    }
  
    return (
      <div className="table-container">
        <table>
          <thead>
            <tr>
              <th>Trace Number</th>
              <th>From</th>
              <th>To</th>
              <th>Amount</th>
              <th>Type</th>
              <th>Date</th>
              <th>Reference</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {transactions.map((transaction) => (
              <tr key={transaction.transactionID}>
                <td><code>{transaction.traceNumber}</code></td>
                <td>
                  <div>{transaction.accountNumberFrom}</div>
                  <small>{transaction.accountNumberTypeFrom}</small>
                </td>
                <td>
                  <div>{transaction.accountNumberTo}</div>
                  <small>{transaction.accountNumberTypeTo}</small>
                </td>
                <td className="amount">
                  ${parseFloat(transaction.amount).toLocaleString('en-US', {
                    minimumFractionDigits: 2
                  })}
                </td>
                <td>
                  <span className={`badge ${transaction.type}`}>
                    {transaction.type}
                  </span>
                </td>
                <td>{new Date(transaction.creationDate).toLocaleDateString()}</td>
                <td>{transaction.reference || '-'}</td>
                <td>
                  <button
                    className="btn-delete"
                    onClick={() => onDelete(transaction.transactionID)}
                  >
                    Delete
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    )
  }
  
  export default TransactionTable