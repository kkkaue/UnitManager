import axios from 'axios';

export default {
  async updateHierarchy(units) {
    try {
      await axios.post('/units/update-hierarchy', { units });
    } catch (error) {
      console.error(error);
      throw error;
    }
  }
};