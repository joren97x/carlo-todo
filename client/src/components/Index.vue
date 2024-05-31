<script setup>

import api from '@/axios'
import { useAuthStore } from '@/stores/authStore'
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { formatDistance } from 'date-fns'
const authStore = useAuthStore()

const router = useRouter()
const todos = ref([])
const form = ref({
    user_id: authStore.auth.id,
    name: '',
    description: '',
    level: 'Medium'
})

const updateTodoForm = ref({

})

const levels = [
    'Low',
    'Medium',
    'High',
    'Very High'
]

onMounted(() => {
    getTodos()
})

async function getTodos() {
    const result = await api.get('/todo', {
        headers: {
            Authorization: `Bearer ${authStore.token}`
        }
    })
    console.log(result)
    if(result.status == 200) {
        todos.value = result.data.todos
    }
}

async function newTodo() {
    const result = await api.post('/todo', form.value, {
        headers: {
            Authorization: `Bearer ${authStore.token}`
        }
    })
    if(result.status == 200) {
        getTodos()
        resetForm()
        const modalElement = document.getElementById('exampleModal');
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        modalInstance.hide();
    }
    console.log(result)
}

async function deleteTodo(id) {
    const result = await api.delete(`/todo/${id}`, {
        headers: {
            Authorization: `Bearer ${authStore.token}`
        }
    })
    if(result.status == 200) {
        getTodos()
        const modalElement = document.getElementById(`deleteTodo${id}`);
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        modalInstance.hide();
    }
    console.log(result)
}

async function updateStatus(id) {
    const result = await api.put(`/todo/update-status/${id}`, {}, {
        headers: {
            Authorization: `Bearer ${authStore.token}`
        }
    })
    if(result.status == 200) {
        getTodos()
    }
    console.log(result)
}

function resetForm() {
    form.value = {
        user_id: authStore.auth.id,
        name: '',
        description: '',
        level: 'Medium'
    }
}

async function updateTodo(todo) {
    console.log(todo)
    const result = await api.put(`/todo/${todo.id}`, todo, {
        headers: {
            Authorization: `Bearer ${authStore.token}`
        }
    })
    console.log(result)
    if(result.status == 200) {
        getTodos()
        const modalElement = document.getElementById(`editTodo${todo.id}`);
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        modalInstance.hide();
    }
}

async function logout() {
    const result = await api.post('/logout', [], {
        headers: {
            Authorization: `Bearer ${authStore.token}`
        }
    })
    console.log(result)
    if(result.status == 200) {
        localStorage.removeItem('auth')
        localStorage.removeItem('token')
        authStore.auth = localStorage.getItem('auth')
        authStore.token = localStorage.getItem('token')
        router.push('/login')
    }
}

</script>

<template>
    <div>
        {{ authStore.auth }}
        {{ authStore.token }}
        <p class="text-h4">Hi, {{ authStore.auth?.name }}</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
            New Todo
        </button>
        
        <button class="btn btn-danger" @click="logout()">Logout</button>

        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Level</th>
                    <th>Status</th>
                    <th>Created at</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="todo in todos" :key="todo.id">
                    <td>{{ todo.name }}</td>
                    <td>{{ todo.description }}</td>
                    <td>{{ todo.level }}</td>
                    <td>{{ todo.status }}</td>
                    <td>{{ formatDistance(todo.created_at, new Date()) }} ago</td>
                    <td>
                        <button class="btn btn-success" @click="updateStatus(todo.id)">
                            {{ todo.status ? 'Wait its not done' : 'Done' }}
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" :data-bs-target="`#editTodo${todo.id}`">
                            Edit
                        </button>
                        <div class="modal fade" :id="`editTodo${todo.id}`" tabindex="-1" aria-labelledby="editTodoLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form @submit.prevent="updateTodo(todo)">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Todo</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="text" class="form-control" v-model="todo.name" placeholder="Name">
                                            <input type="text" class="form-control" v-model="todo.description" placeholder="Description">
                                            <select v-model="todo.level" class="form-select" placeholder="Level">
                                                <option :value="level" v-for="level in levels" :key="level">{{ level }}</option>
                                            </select>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Add</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" :data-bs-target="`#deleteTodo${todo.id}`">
                            Delete
                        </button>
                        <div class="modal fade" :id="`deleteTodo${todo.id}`" tabindex="-1" aria-labelledby="deleteTodoLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form @submit.prevent="deleteTodo(todo.id)">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Delete Todo</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to delete {{ todo.name }}
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Delete</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" class="text-center" v-if="todos.length == 0">
                        No todos found
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form @submit.prevent="newTodo()">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">New Todo</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="text" class="form-control" v-model="form.name" placeholder="Name">
                            <input type="text" class="form-control" v-model="form.description" placeholder="Description">
                            <select v-model="form.level" class="form-select" placeholder="Level">
                                <option :value="level" v-for="level in levels" :key="level">{{ level }}</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
    </div>
</template>