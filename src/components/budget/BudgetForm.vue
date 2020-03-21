<template>
    <div>
        <div v-if="!loading" class="add-edit-budget">
            <h2>{{ budget.id ? 'Edit' : 'Add' }} Budget</h2>
            <input v-model="budget.name" type="text" placeholder="Name" title="Name" />
            <textarea v-model="budget.description" placeholder="Description" title="Description"></textarea>
            <div class="sharing">
                <h3>Sharing</h3>
                <input v-model="user" v-on:keyup.enter="addPermission()" type="test" placeholde="User" title="User" />
                <ul v-if="budget.users" class="sharing-users">
                    <li v-for="user in budget.users">
                        <span v-if="user.user">
                            {{ user.user }}
                        </span>
                        <span v-if="user.permission">
                        : {{ user.permission }}
                        </span>
                    </li>
                </ul>
            </div>
            <button @click="saveBudget()">Save Budget</button>
        </div>
        <div v-if="loading" class="icon-loading"></div>
    </div>
</template>
<script>
import { mapGetters } from "vuex";

export default {
    name: "add-edit-budget",
    components: {
    },
    data: function() {
        return {
            saving: false,
            user: undefined
        };
    },
    props: {
        budget: Object,
    },
    computed: {
        loading: state => state.budget === undefined || state.saving
    },
    methods: {
        addPermission() {
            const user = this.user
            this.user = undefined;
            this.budget.users = this.budget.users.filter(u => u.user != user)
            this.budget.users.push({
                "user": user,
                "permission": 2
            })
        },
        saveBudget() {
            this.saving = true;
            this.$store.dispatch("budgetFormSaveClicked", this.budget);
        }
    },
    mounted() {
        let budgetId;
        if (this.budget) {
            budgetId = this.budget.id;
        }
    }
};
</script>
<style scoped>
.add-edit-budget > * {
    display: block;
    width: 100%;
    max-width: 500px;
}
.radio-container {
    display: flex;
    align-items: center;
}

.radio-container label {
    margin-right: 1em;
}
.icon-loading {
    margin-top: 16px;
}
</style>
