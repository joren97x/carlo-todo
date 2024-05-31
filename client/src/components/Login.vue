<script setup>

import { reactive, ref } from 'vue'
import api from '../axios.js'
import { useAuthStore } from '../stores/authStore.js'
import { useRouter } from 'vue-router'

const router = useRouter()
const authStore = useAuthStore()
const loginButtonLoading = ref(false)
const showPassword = ref(false)
const form = reactive({
    email: null,
    password: null
})
const errors = ref(null)

function login() {
    try {
        loginButtonLoading.value = true
        api.post('/login', form)
        .then((res) => {
            loginButtonLoading.value = true
            authStore.setAuth(res.data)
            router.push('/')
        }).catch((err) => {
            loginButtonLoading.value = false
            console.error(err)
            if(err.response) {
                errors.value = err.response.data.errors
                if(!err.response.data.errors) {
                    errors.value = { email: 'Invalid credentials' }
                }
            }
            else {
                errors.value = { server: 'Internal server error' }
            }
        })

    }
    catch(err) {
        loginButtonLoading.value = false
        console.error(err)
        errors.value = err.response.data.message
    }
}


</script>

<template>
    <div>
        {{ error }}
        <form @submit.prevent="login()">
            <input type="text" placeholder="Email" v-model="form.email">
            <input type="text" placeholder="Password" v-model="form.password">
            <button type="submit">Login</button>
            <router-link to="/register">Register</router-link>
        </form>
    </div>
</template>