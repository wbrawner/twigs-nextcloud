<template>
  <ul>
    <li v-for="category in categories" :key="category.id">
      <p>{{ category.name }}</p>
    </li>
  </ul>
</template>
<script>
import { mapGetters } from "vuex";

export default {
  name: "category-list",
  components: {},
  props: {
    budgetId: Number,
    expense: Boolean
  },
  computed: {
    categories: function(state) {
        const categories = this.$store.getters.categories(this.budgetId)
        if (categories) {
            return categories.filter(category => category.expense === this.expense)
        } else {
            return [];
        }      
    }
  }
};
</script>
