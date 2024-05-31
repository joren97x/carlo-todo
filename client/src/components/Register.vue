<script setup>

import { reactive, ref } from 'vue'
import { useAuthStore } from '../stores/authStore'
import { useRouter } from 'vue-router'
import api from '../axios'

const router = useRouter()
const authStore = useAuthStore()
const showPassword = ref(false)
const registerButtonLoading = ref(false)
const form = reactive({
    email: null,
    name: null,
    password: null
})

const errors = ref(null)

function register() {
    try {
        registerButtonLoading.value = true
        api.post('/register', form)
        .then((res) => {
            console.log(res)
            registerButtonLoading.value = true
            authStore.setAuth(res.data)
            router.push('/')
        }).catch((err) => {
            console.log(err)
            if(err.response) {
                errors.value = err.response.data.errors
            }
            else {
                errors.value = { server: 'Internal server error' }
            }
            registerButtonLoading.value = false
            console.error(err)
        })

    }
    catch(err) {
        errors.value = "Please fill in required fields"
        registerButtonLoading.value = false
        console.error(err)
    }
}

</script>

<template>
    <div>
        Register page
        {{ errors }}

        <form @submit.prevent="register()">
            <input type="text" placeholder="Email" v-model="form.email">
            <input type="text" placeholder="Name" v-model="form.name">
            <input type="text" placeholder="Password" v-model="form.password">
            <button type="submit">Register</button>
            <router-link to="/login">Login</router-link>
        </form>
    </div>
</template>