<template>
    <div>
        <div v-if="!loading" class="add-edit-transaction">
            <h2>{{ transaction.id ? 'Edit' : 'Add' }} Transaction</h2>
            <input v-model="transaction.name" type="text" placeholder="Name" title="Name" />
            <textarea v-model="transaction.description" placeholder="Description" title="Description"></textarea>
            <input v-model.number="transaction.amount" type="number" placeholder="Amount" title="Amount" />
            <DatetimePicker :value="transaction.date" type="datetime" />
            <div class="radio-container">
                <input v-model="transaction.expense" type="radio" id="expense" :value="true" />
                <label for="expense">Expense</label>
                <input v-model="transaction.expense" type="radio" id="income" :value="false" />
                <label for="income">Income</label>
            </div>
            <select v-model="transaction.budgetId" v-on:change="updateCategories()">
                <option disabled value>Select a budget</option>
                <option v-for="budget in budgets" :key="budget.id" :value="budget.id">{{ budget.name }}</option>
            </select>
            <select v-model="transaction.categoryId">
                <option disabled value>Select a category</option>
                <option
                    v-for="category in filteredCategories"
                    :key="category.id"
                    :value="category.id"
                    >{{ category.name }}</option>
            </select>
            <button @click="saveTransaction()">Save Transaction</button>
        </div>
        <div v-if="loading" class="icon-loading"></div>
    </div>
</template>
<script>
import { DatetimePicker } from "@nextcloud/vue/dist/Components/DatetimePicker";
import { mapGetters } from "vuex";

export default {
    name: "add-edit-transaction",
    components: {
        DatetimePicker
    },
    data: function() {
        return {
            saving: false
        };
    },
    props: {
        transaction: Object
    },
    computed: {
        ...mapGetters(["budgets"]),
        filteredCategories: function(state) {
            return this.$store.getters.categories.filter(function(category) {
                return category.expense === state.transaction.expense;
            });
        },
        loading: state => state.transaction === undefined || state.saving
    },
    methods: {
        updateCategories() {
            if (!this.transaction) return;
            this.$store.dispatch(
                "addEditTransactionBudgetSelected",
                this.transaction.budgetId
            );
        },
        saveTransaction() {
            this.saving = true;
            this.$store.dispatch("transactionFormSaveClicked", this.transaction);
        }
    },
    mounted() {
        let transactionId;
        if (this.transaction) {
            transactionId = this.transaction.id;
        }
        this.updateCategories();
    }
};
</script>
<style scoped>
.add-edit-transaction > * {
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
