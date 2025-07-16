const API_BASE_URL = 'http://localhost:8000/api';

export const api = {
  categories: {
    getAll: () => fetch(`${API_BASE_URL}/categories`).then(r => r.json()),
    getById: (id) => fetch(`${API_BASE_URL}/categories/${id}`).then(r => r.json())
  },
  products: {
    getAll: (params) =>
      fetch(`${API_BASE_URL}/products?${new URLSearchParams(params)}`).then(r => r.json()),
    getById: (id) => fetch(`${API_BASE_URL}/products/${id}`).then(r => r.json()),
  },
  orders: {
    create: (data, token) =>
      fetch(`${API_BASE_URL}/orders`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify(data)
      }).then(r => r.json()),
    getAll: (token) =>
      fetch(`${API_BASE_URL}/orders`, {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      }).then(r => r.json()),
  }
};

