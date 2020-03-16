import VueRouter from 'vue-router'
import Vue from 'vue'
import BudgetDetails from '../components/BudgetDetails'
import CategoryDetails from '../components/CategoryDetails'
import AddEditTransaction from '../components/AddEditTransaction'
import TransactionDetails from '../components/TransactionDetails'

Vue.use(VueRouter)

const routes = [
    {
        path: '/budgets/:id',
        name: 'budgetDetails',
        component: BudgetDetails,
    },
    {
        path: '/categories/:id',
        name: 'categoryDetails',
        component: CategoryDetails,
    },
    {
        path: '/transactions/new',
        name: 'newTransaction',
        component: AddEditTransaction,
    },
    {
        path: '/transactions/:id',
        name: 'transactionDetails',
        component: TransactionDetails,
    },
    {
        path: '/transactions/:id/edit',
        name: 'editTransaction',
        component: AddEditTransaction,
    },
]

export default new VueRouter({
    linkActiveClass: 'active',
    routes,
})
