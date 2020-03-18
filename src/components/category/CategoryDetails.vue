<template>
    <div v-if="category" class="category-details">
        <div class="header-info">
            <h2>{{ category.name }}</h2>
            <h3 v-if="balance">Balance: {{ balance.toLocaleString(undefined, {style: 'currency', currency: 'USD'}) }}</h3>
            <div class="actions">
                <button @click="addTransaction()"><span class="icon-add"></span> Add Transaction</button>
                <Actions>
                <ActionButton icon="icon-edit" text="Edit" @click="editCategory()">Edit</ActionButton>
                <ActionButton icon="icon-delete" text="Delete" @click="alert('Delete')">Delete</ActionButton>
                </Actions>
            </div>
        </div>
        <h3>Transactions</h3>
        <TransactionList :category-id="category.id"></TransactionList>
    </div>
</template>
<script>
import { mapGetters, mapState } from "vuex";
import { Actions } from "@nextcloud/vue/dist/Components/Actions";
import { ActionButton } from "@nextcloud/vue/dist/Components/ActionButton";
import TransactionList from '../transaction/TransactionList'

export default {
    name: "category-details",
    components: {
        Actions,
        ActionButton,
        TransactionList
    },
    computed: {
        ...mapState(["categories", "currentCategory"]),
        category: function(state) {
            if (state.categories.length === 0 || !state.currentCategory) {
                return false;
            }
            return state.categories.find((category) => category.id === Number.parseInt(state.currentCategory));
        },
        balance: function(state) {
            if (!state.currentCategory) {
                return 0;
            }
            return this.$store.getters.categoryBalance(state.currentCategory) / 100;
        }
    },
    methods: {
        load() {
            this.$store.dispatch("categoryDetailsViewed", this.$route.params.id);
        },
        addTransaction() {
            this.$store.dispatch('addTransactionClicked')
        },
        editCategory() {
            this.$store.dispatch('editCategoryClicked', this.$route.params.id);
        }
    },
    mounted() {
        console.log("CategoryDetails mounted")
        this.load();
    }
};
</script>
