<template>
  <div>
    <AppNavigationItem
      v-for="budget in budgets"
      :key="budget.id"
      :title="budget.name"
      v-on:click="view(budget.id)"
    ></AppNavigationItem>
    <AppNavigationNew text="New Budget"></AppNavigationNew>
  </div>
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
    view: function(id) {
      this.$router.push({ name: "budgetDetails", params: { id: id } })
    },
    new: function() {}
  },
  mounted() {
    this.load()
  }
};
</script>
