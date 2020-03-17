<template>
  <div>
    <div v-if="!loading" class="add-edit-category">
      <h2>{{ category.id ? 'Edit' : 'Add' }} Category</h2>
      <input v-model="category.name" type="text" placeholder="Name" title="Name" />
      <input v-model.number="category.amount" type="number" placeholder="Amount" title="Amount" />
      <div class="radio-container">
        <input v-model="category.expense" type="radio" id="expense" :value="true" />
        <label for="expense">Expense</label>
        <input v-model="category.expense" type="radio" id="income" :value="false" />
        <label for="income">Income</label>
      </div>
      <select v-model="category.budgetId" v-on:change="updateCategories()">
        <option disabled value>Select a budget</option>
        <option v-for="budget in budgets" :key="budget.id" :value="budget.id">{{ budget.name }}</option>
      </select>
      <button @click="saveCategory()">Save Category</button>
    </div>
    <div v-if="loading" class="icon-loading"></div>
  </div>
</template>
<script>
import { mapGetters } from "vuex";

export default {
  name: "add-edit-category",
  components: {
  },
  data: function() {
    return {
      saving: false
    };
  },
  props: {
    category: Object
  },
  computed: {
    ...mapGetters(["budgets"]),
    loading: state => state.category === undefined || state.saving
  },
  methods: {
    saveCategory() {
      this.saving = true;
      this.$store.dispatch("categoryFormSaveClicked", this.category);
    }
  },
  mounted() {
    let categoryId;
    if (this.category) {
      categoryId = this.category.id;
    }
  }
};
</script>
<style scoped>
.add-edit-category > * {
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