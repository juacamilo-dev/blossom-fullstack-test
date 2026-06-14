function Filters({ filters, onFilterChange, onApply, onClear }) {
    return (
      <div className="filters">
        <div className="filters-row">
          <div className="filter-group">
            <label>Type</label>
            <select
              value={filters.type}
              onChange={(e) => onFilterChange('type', e.target.value)}
            >
              <option value="">All</option>
              <option value="credit">Credit</option>
              <option value="debit">Debit</option>
            </select>
          </div>
  
          <div className="filter-group">
            <label>Date From</label>
            <input
              type="date"
              value={filters.date_from}
              onChange={(e) => onFilterChange('date_from', e.target.value)}
            />
          </div>
  
          <div className="filter-group">
            <label>Date To</label>
            <input
              type="date"
              value={filters.date_to}
              onChange={(e) => onFilterChange('date_to', e.target.value)}
            />
          </div>
  
          <div className="filter-actions">
            <button className="btn-primary" onClick={onApply}>
              Apply Filters
            </button>
            <button className="btn-secondary" onClick={onClear}>
              Clear
            </button>
          </div>
        </div>
      </div>
    )
  }
  
  export default Filters