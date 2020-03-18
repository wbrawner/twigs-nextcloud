<template>
  <div v-if="transaction" class="transaction-details">
    <h2>{{ transaction.name }}</h2>
    <h3
      :class="transaction.expense ? 'danger' : 'good'"
    >{{ (transaction.amount / 100).toLocaleString(undefined, {style: 'currency', currency: 'USD'}) }} {{ transaction.expense ? 'Expense' : 'Income' }}</h3>
    <p class="transaction-info date">{{ new Date(transaction.date).toLocaleDateString() }}</p>
    <p class="transaction-info description">{{ transaction.description }}</p>
    <p v-if="category" class="transaction-info category">Category: {{ category.name }}</p>
    <p v-if="budget" class="transaction-info budget">Budget: {{ budget.name }}</p>
    <p class="transaction-info registered-by">
      Registered By:
      <UserBubble
        :user="transaction.createdBy"
        :display-name="transaction.createdBy"
      ></UserBubble>
      {{ new Date(transaction.createdDate).toLocaleDateString() }}
    </p>
    <p v-if="transaction.updatedBy" class="transaction-info updated-by">
      Updated By:
      <UserBubble
        :user="transaction.updatedBy"
        :display-name="transaction.updatedBy"
      ></UserBubble>
      {{ new Date(transaction.updatedDate).toLocaleDateString() }}
    </p>
  </div>
</template>
<script>
import { mapGetters, mapState } from "vuex";
import { UserBubble } from "@nextcloud/vue/dist/Components/UserBubble";

export default {
  name: "transaction-details",
  components: {
    UserBubble
  },
  computed: {
    ...mapGetters(["transaction"]),
    category: function(state, getters) {
      const transaction = this.$store.getters.transaction
      if (!transaction || !transaction.categoryId) {
        return undefined;
      }
        return this.$store.getters.categories.find(category => category.id === transaction.categoryId)
    },
    budget: function(state) {
      const transaction = this.$store.getters.transaction
      if (!transaction || !transaction.budgetId) {
        return undefined;
      }
        return this.$store.getters.budgets.find(budget => budget.id === transaction.budgetId)
    }
  },
  methods: {
    load() {
      this.$store.dispatch("transactionDetailsViewed", this.$route.params.id);
    }
  },
  mounted() {
    this.load();
  }
};
</script>
