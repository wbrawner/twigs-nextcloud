<template>
	<ul>
		<AppNavigationNew text="New Budget" @click="newBudget()" />
		<AppNavigationItem
			v-for="budget in budgets"
			:key="budget.id"
			:title="budget.name"
			:to="{ name: 'budgetDetails', params: { id: budget.id } }" />
	</ul>
</template>
<script>
import { AppNavigationItem } from '@nextcloud/vue/dist/Components/AppNavigationItem'
import { AppNavigationNew } from '@nextcloud/vue/dist/Components/AppNavigationNew'
import { mapGetters } from 'vuex'

export default {
    name: 'BudgetList',
    components: {
        AppNavigationItem,
        AppNavigationNew,
    },
    computed: {
        ...mapGetters(['budgets']),
    },
    mounted() {
        this.load()
    },
    methods: {
        load: function() {
            this.$store.dispatch('budgetListViewed')
        },
        newBudget: function() {
            this.$store.dispatch('addBudgetClicked')
        },
    },
}
</script>
