<template>
	<div>
		<AppNavigation>
			<BudgetList />
			<AppNavigationSettings />
		</AppNavigation>
		<AppContent>
			<RouterView />
		</AppContent>
	</div>
</template>
<script>
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation'
import AppNavigationSettings from '@nextcloud/vue/dist/Components/AppNavigationSettings'
import AppContent from '@nextcloud/vue/dist/Components/AppContent'
import BudgetList from './components/budget/BudgetList'

export default {
    components: {
        AppNavigation,
        AppNavigationSettings,
        AppContent,
        BudgetList,
    },
}
</script>
<style>
.app-twigs {
  flex-grow: 1;
}

.app-twigs > div {
  width: 100%;
}

</style>
