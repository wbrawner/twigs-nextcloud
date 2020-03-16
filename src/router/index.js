import VueRouter from 'vue-router'
import Vue from 'vue'
import BudgetDetails from '../components/BudgetDetails'
import CategoryDetails from '../components/CategoryDetails'
import NewTransaction from '../components/transaction/NewTransaction'
import EditTransaction from '../components/transaction/EditTransaction'
import TransactionDetails from '../components/transaction/TransactionDetails'

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
        component: NewTransaction,
    },
    {
        path: '/transactions/:id',
        name: 'transactionDetails',
        component: TransactionDetails,
    },
    {
        path: '/transactions/:id/edit',
        name: 'editTransaction',
        component: EditTransaction,
    },
]

export default new VueRouter({
    linkActiveClass: 'active',
    routes,
})
