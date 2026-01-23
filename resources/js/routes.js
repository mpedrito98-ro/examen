/*import { createWebHistory, createRouter } from "vue-router";

//auto register components to vue router
const components = import.meta.glob('../components/*.vue', {eager: true})
let routes =[
    {path:'/', name: 'Home', component: ()=> import('../components/About.vue')}
]
Object.entries(components).forEach(([path, definition]) => {
    const componentName = path.split('/').pop().replace(/\.\w+$/, '')
    routes.push({path:'/'+componentName.toLowerCase(), name:componentName, component:() =>import(`../components/${componentName}.vue`)})
})

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;*/


import { createWebHistory, createRouter } from "vue-router";

// Auto register components to Vue router
const componentModules = {
    ...import.meta.glob('../components/*.vue', {eager: true}),
    ...import.meta.glob('./components/questions/*.vue', {eager: true}),
    ...import.meta.glob('./components/exam/*.vue', {eager: true}),
    ...import.meta.glob('../admin/colores/*.vue', {eager: true}),
};

let routes = [
    { path: '/', name: 'Home', component: () => import('../components/About.vue') }
];

Object.entries(componentModules).forEach(([path, definition]) => {
    const componentName = path.split('/').pop().replace(/\.\w+$/, '');
    const lowerCaseName = componentName.toLowerCase();

    if (path.includes('./components/questions/')) {
        const routePath = lowerCaseName === 'index' ? '/questions' : '/questions/' + lowerCaseName;
        routes.push({
            path: routePath,
            name: 'Question' + componentName,
            component: () => import(`./components/questions/${componentName}.vue`)
        });
    } else if (path.includes('./components/exam/')) {
        const routePath = lowerCaseName === 'index' ? '/exam' : '/exam/' + lowerCaseName;
        routes.push({
            path: routePath,
            name: 'Exam' + componentName,
            component: () => import(`./components/exam/${componentName}.vue`)
        });
    } else if (path.includes('../components/')) {
        routes.push({
            path: '/' + lowerCaseName,
            name: componentName,
            component: () => import(`../components/${componentName}.vue`)
        });
    } else if (path.includes('../admin/colores/')) {
        routes.push({
            path: '/admin/colores/' + lowerCaseName,
            name: 'Admin' + componentName,
            component: () => import(`../admin/colores/${componentName}.vue`)
        });
    }
});

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;