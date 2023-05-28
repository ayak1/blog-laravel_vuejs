export default {
    async post(endpoint, data,token) {
      const response = await fetch(`http://127.0.0.1:8000/api/${endpoint}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`,
        },
        body: JSON.stringify(data)
      });
      console.log('rr')
      if (!response.ok) {
        throw new Error(`${response.status}: ${response.statusText}`);
      }
      const responseData = await response.json();
      return responseData;
    }
  };