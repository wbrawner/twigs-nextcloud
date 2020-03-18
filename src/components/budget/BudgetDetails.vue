<template>
    <div v-if="budget">
        <div class="header">
            <div class="header-info">
                <h2>{{ budget.name }}</h2>
                <p>{{ budget.description }}</p>
                <h3
                    v-if="balance"
                    >Balance: {{ balance.toLocaleString(undefined, {style: 'currency', currency: 'USD'}) }}</h3>
            </div>
            <div class="actions">
                <button @click="addTransaction()"><span class="icon-add"></span> Add Transaction</button>
                <button @click="addCategory()"><span class="icon-add"></span> Add Category</button>
                <Actions>
                <ActionButton icon="icon-edit" text="Edit" @click="editBudget()">Edit</ActionButton>
                <ActionButton icon="icon-delete" text="Delete" @click="alert('Delete')">Delete</ActionButton>
                </Actions>
            </div>
        </div>
        <div class="budget-details">
            <div class="card income">
                <h3>Income</h3>
                <CategoryList v-bind:budget-id="budget.id" v-bind:expense="false"></CategoryList>
            </div>
            <div class="card expenses">
                <h3>Expenses</h3>
                <CategoryList v-bind:budget-id="budget.id" v-bind:expense="true"></CategoryList>
            </div>
            <div class="card transactions">
                <h3>Recent Transactions</h3>
                <TransactionList :budget-id="budget.id" :limit="5"></TransactionList>
            </div>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapState } from "vuex";
import { Actions } from "@nextcloud/vue/dist/Components/Actions";
import { ActionButton } from "@nextcloud/vue/dist/Components/ActionButton";
import CategoryList from "../category/CategoryList";
import TransactionList from "../transaction/TransactionList";

export default {
    name: "budget-details",
    components: {
        Actions,
        ActionButton,
        CategoryList,
        TransactionList
    },
    computed: {
        ...mapState(["budgets", "currentBudget"]),
        ...mapGetters(["budget"]),
        balance: function(state) {
            if (!state.currentBudget) {
                return 0;
            }
            return this.$store.getters.budgetBalance(state.currentBudget) / 100;
        }
    },
    methods: {
        load() {
            this.$store.dispatch("budgetDetailsViewed", this.$route.params.id);
        },
        addTransaction() {
            this.$store.dispatch('addTransactionClicked')
        },
        addCategory() {
            this.$store.dispatch('addCategoryClicked')
        },
        editBudget() {
            this.$store.dispatch('editBudgetClicked', this.$route.params.id);
        }
    },
    mounted() {
        this.load();
    }
};
</script>
<style scoped>
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.budget-details {
    display: grid;
    justify-content: space-between;
    grid-gap: 0.5em;
    grid-template-columns: repeat(2, 1fr);
    padding: 0.5em;
}

.card {
    overflow: hidden;
    border: 1px solid var(--color-border);
    border-radius: var(--border-radius);
}

.income {
    grid-column: 1;
}

.expenses {
    grid-column: 2;
}

.transactions {
    grid-column: 1;
}
</style>
