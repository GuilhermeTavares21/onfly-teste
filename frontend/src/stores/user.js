import { defineStore } from 'pinia';

export const useUserStore = defineStore('user', {
  state: () => ({
    user: null,
    token: null,
  }),
  getters: {
    isLoggedIn: (state) => !!state.token,
    isAdmin: (state) => state.user?.is_admin,
  },
  actions: {
    setUser(user, token) {
      this.user = user;
      this.token = token;
    },
    logout() {
      this.user = null;
      this.token = null;
    },
  },
});
