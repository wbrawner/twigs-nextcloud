import VueRouter from 'vue-router'
import Vue from 'vue'
import NewBudget from '../components/budget/NewBudget'
import EditBudget from '../components/budget/EditBudget'
import BudgetDetails from '../components/budget/BudgetDetails'
import NewCategory from '../components/category/NewCategory'
import EditCategory from '../components/category/EditCategory'
import CategoryDetails from '../components/category/CategoryDetails'
import NewTransaction from '../components/transaction/NewTransaction'
import EditTransaction from '../components/transaction/EditTransaction'
import TransactionDetails from '../components/transaction/TransactionDetails'

Vue.use(VueRouter)

const routes = [
    {
        path: '/budgets/new',
        name: 'newBudget',
        component: NewBudget,
    },
    {
        path: '/budgets/:id',
        name: 'budgetDetails',
        component: BudgetDetails,
    },
    {
        path: '/budgets/:id/edit',
        name: 'editBudget',
        component: EditBudget,
    },
    {
        path: '/categories/new',
        name: 'newCategory',
        component: NewCategory,
    },
    {
        path: '/categories/:id',
        name: 'categoryDetails',
        component: CategoryDetails,
    },
    {
        path: '/categories/:id/edit',
        name: 'editCategory',
        component: EditCategory,
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
