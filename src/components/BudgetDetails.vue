<template>
  <div>
    <h1 v-if="budget" v-text="budget.name"></h1>
    <div class="card">
      
    </div>
  </div>
</template>
<script>
import { mapGetters } from "vuex";

export default {
  name: "budget-details",
  components: {
  },
  computed: {
    budget: function(state) {
      const budgetId = this.$route.params.id
      const budget = this.$store.getters.budgets.find(budget => budget.id === budgetId);
      return budget;
    }
  },
  methods: {
  }
};
</script>