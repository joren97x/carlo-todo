import { defineStore } from "pinia"

export const useAuthStore = defineStore('auth', {
    state: () => ({
        auth: JSON.parse(localStorage.getItem('auth')),
        token: JSON.parse(localStorage.getItem('token')),
    }),
    getters: {
        getToken: (state) => {
            return state.token
        },
        isAuthenticated: (state) => {
            return state.auth
        }
    },
    actions: {
        setToken(token) {
            this.token = token
        },
        setUser(user) {
            this.auth = user
        },
        setAuth(data) {
            console.log(data)
            this.setUser(data.user)
            this.setToken(data.token)
            localStorage.setItem('auth', JSON.stringify(data.user))
            localStorage.setItem('token', JSON.stringify(data.token))
        }
    }
})