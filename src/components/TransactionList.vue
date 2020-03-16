<template>
  <ul>
    <li v-for="transaction in filteredTransactions" :key="transaction.id">
      <a v-on:click="view(transaction.id)" class="transaction">
        <div class="transaction-details">
          <p class="transaction-name">{{ transaction.name }}</p>
          <p class="transaction-date">{{ new Date(transaction.date).toLocaleDateString() }}</p>
        </div>
        <p
          class="transaction-amount"
          :class="transaction.expense ? 'danger' : 'good'"
        >{{ (transaction.amount / 100).toLocaleString(undefined, {style: 'currency', currency: 'USD'}) }}</p>
      </a>
    </li>
  </ul>
</template>
<script>
import { mapGetters, mapState } from "vuex";

export default {
  name: "transaction-list",
  components: {
  },
  props: {
    budgetId: Number,
    categoryId: Number,
  },
  computed: {
    ...mapState(["transactions", "currentTransaction"]),
    filteredTransactions: function(state) {
      return state.transactions.filter(function(transaction) {
        console.log(transaction.date)
          if (state.budgetId) {
            return transaction.budgetId === state.budgetId
          }
          if (state.categoryId) {
            return transaction.categoryId === state.categoryId
          }
          return false
        }
      );
    }
  },
  methods: {
    view: function(id) {
      this.$store.dispatch("transactionClicked", id);
    }
  }
};
</script>
<style>
.transaction {
  padding: 0.5em;
  height: 4em;
  display: flex;
  justify-content: space-between;
  align-items: center;
  cursor: pointer;
}

.transaction * {
  cursor: pointer;
}

.transaction:hover {
  background: var(--color-background-hover);
}

.transaction-details {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
</style>