<template>
  <div v-if="category" class="category-details">
      <h2>{{ category.name }}</h2>
      <h3 v-if="balance">Balance: {{ balance.toLocaleString(undefined, {style: 'currency', currency: 'USD'}) }}</h3>
      <h3>Transactions</h3>
      <TransactionList :category-id="category.id"></TransactionList>
  </div>
</template>
<script>
import { mapGetters, mapState } from "vuex";
import TransactionList from '../transaction/TransactionList'

export default {
  name: "category-details",
  components: {
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
    }
  },
  mounted() {
    console.log("CategoryDetails mounted")
    this.load();
  }
};
</script>
