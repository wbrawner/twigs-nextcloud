import VueRouter from 'vue-router'
import Vue from 'vue'
import BudgetDetails from '../components/BudgetDetails'

Vue.use(VueRouter);

const routes = [
    {
        path: '/budgets/:id',
        name: 'budgetDetails',
        component: BudgetDetails
    }
]

export default new VueRouter({
    linkActiveClass: 'active',
    routes,
})
