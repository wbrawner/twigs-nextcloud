<template>
  <div v-if="budget">
    <h1 v-text="budget.name"></h1>
    <div class="card">
      <CategoryList v-bind:budget-id="budget.id" v-bind:expense="true"></CategoryList>
    </div>
    <div class="card">
      <CategoryList v-bind:budget-id="budget.id" v-bind:expense="false"></CategoryList>
    </div>
  </div>
</template>
<script>
import { mapGetters } from "vuex";
import CategoryList from "./CategoryList";

export default {
  name: "budget-details",
  components: {
    CategoryList
  },
  computed: {
    budget: function(state) {
      const budgetId = this.$route.params.id;
      const budget = this.$store.getters.budgets.find(
        budget => budget.id === budgetId
      );
      return budget;
    }
  },
  methods: {
    load() {
      this.$store.dispatch("budgetDetailsViewed", this.$route.params.id);
    }
  },
  mounted() {
      this.load()
  }
};
</script>