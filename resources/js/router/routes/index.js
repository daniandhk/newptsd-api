import etcRoutes from "./etc.js";
import authRoutes from "./auth.js";
import patientRoutes from "./patient.js";
import psychologistRoutes from "./psychologist.js";
import adminRoutes from "./admin.js";
import settingsRoutes from "./settings";

const baseRoutes = [
    {
        path: '/',
        name: 'home',
        meta: {
            authRequired: true,
        },
        component: () => import('../../views/pages/dashboard')
    },
    {
        path: '/mail-admin',
        name: 'mail-admin',
        beforeEnter() {location.href = 'mailto:daniandhika03@gmail.com?subject=Bantuan%20helpPTSD'}
    },
]

const routes = baseRoutes.concat(etcRoutes, authRoutes, patientRoutes, psychologistRoutes, adminRoutes, settingsRoutes);
export default routes;