<template>
  <div>
    <div v-if="!loading" class="add-edit-budget">
      <h2>{{ budget.id ? 'Edit' : 'Add' }} Budget</h2>
      <input v-model="budget.name" type="text" placeholder="Name" title="Name" />
      <textarea v-model="budget.description" placeholder="Description" title="Description"></textarea>
      <button @click="saveBudget()">Save Budget</button>
    </div>
    <div v-if="loading" class="icon-loading"></div>
  </div>
</template>
<script>
import { mapGetters } from "vuex";

export default {
  name: "add-edit-budget",
  components: {
  },
  data: function() {
    return {
      saving: false
    };
  },
  props: {
    budget: Object
  },
  computed: {
    loading: state => state.budget === undefined || state.saving
  },
  methods: {
    saveBudget() {
      this.saving = true;
      this.$store.dispatch("budgetFormSaveClicked", this.budget);
    }
  },
  mounted() {
    let budgetId;
    if (this.budget) {
      budgetId = this.budget.id;
    }
  }
};
</script>
<style scoped>
.add-edit-budget > * {
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
