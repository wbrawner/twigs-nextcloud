<template>
    <BudgetForm :budget="budget" />
</template>
<script>
import { mapGetters } from 'vuex'
import BudgetForm from './BudgetForm'

export default {
    name: 'edit-budget',
    components: {
        BudgetForm
    },
    computed: {
        ...mapGetters(['budget'])
    },
    mounted() {
        this.$store.dispatch("editBudgetViewed", this.$route.params.id);
    }
}
</script>
