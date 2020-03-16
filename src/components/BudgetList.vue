<template>
  <ul>
    <AppNavigationNew text="New Budget"></AppNavigationNew>
    <AppNavigationItem
      v-for="budget in budgets"
      :key="budget.id"
      :title="budget.name"
      :to="{ name: 'budgetDetails', params: { id: budget.id } }"
    />
  </ul>
</template>
<script>
import { AppNavigationItem } from "@nextcloud/vue/dist/Components/AppNavigationItem";
import { AppNavigationNew } from "@nextcloud/vue/dist/Components/AppNavigationNew";
import { mapGetters } from "vuex";

export default {
  name: "budget-list",
  components: {
    AppNavigationItem,
    AppNavigationNew,
  },
  computed: {
    ...mapGetters(["budgets"])
  },
  methods: {
    load: function() {
      this.$store.dispatch('budgetListViewed')
    },
    new: function() {}
  },
  mounted() {
    this.load()
  }
};
</script>
