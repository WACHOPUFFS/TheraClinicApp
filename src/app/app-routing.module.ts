import { NgModule } from '@angular/core';
import { PreloadAllModules, RouterModule, Routes } from '@angular/router';

const routes: Routes = [
  { 
    path: '', 
    redirectTo: 'login', 
    pathMatch: 'full' 
  },
  {
    path: 'login',
    loadChildren: () => import('./login/login.module').then(m => m.LoginPageModule),
    data: { breadcrumb: 'Login' }
  },

  {
    path: 'home',
    loadChildren: () => import('./home/home.module').then(m => m.HomePageModule),
    data: { breadcrumb: 'Inicio' }
  },

  {
    path: 'project-control',
    loadChildren: () => import('./project-control/project-control.module').then(m => m.ProjectControlPageModule),
    data: { breadcrumb: 'Control de Proyectos' }
  },
  {
    path: 'assign-projects',
    loadChildren: () => import('./assign-projects/assign-projects.module').then(m => m.AssignProjectsPageModule),
    data: { breadcrumb: 'Asignación de Proyectos' }
  },

  {
    path: 'employee-control',
    loadChildren: () => import('./employee-control/employee-control.module').then(m => m.EmployeeControlPageModule),
    data: { breadcrumb: 'Control de Empleados' }
  },
  {
    path: 'add-employee',
    loadChildren: () => import('./add-employee/add-employee.module').then(m => m.AddEmployeePageModule),
    data: { breadcrumb: 'Registrar Solicitudes de Empleados' }
  },
  {
    path: 'edit-employee',
    loadChildren: () => import('./edit-employee/edit-employee.module').then(m => m.EditEmployeePageModule),
    data: { breadcrumb: 'Editar Solicitudes de Empleados' }
  },

  {
    path: 'settings',
    loadChildren: () => import('./settings/settings.module').then(m => m.SettingsPageModule),
    data: { breadcrumb: 'Configuraciones' }
  },
  {
    path: 'company-settings',
    loadChildren: () => import('./company-settings/company-settings.module').then(m => m.CompanySettingsPageModule),
    data: { breadcrumb: 'Configuración de Empresa' }
  },
  {
    path: 'upload-logo',
    loadChildren: () => import('./upload-logo/upload-logo.module').then(m => m.UploadLogoPageModule),
    data: { breadcrumb: 'Asignar Logo' }
  },

  {
    path: 'user-permissions-sections',
    loadChildren: () => import('./user-permissions-sections/user-permissions-sections.module').then(m => m.UserPermissionsSectionsPageModule),
    data: { breadcrumb: 'Secciones Visibles de los Perfiles' }
  },
 
  {
    path: 'company-permissions-sections',
    loadChildren: () => import('./company-permissions-sections/company-permissions-sections.module').then(m => m.CompanyPermissionsSectionsPageModule),
    data: { breadcrumb: 'Secciones Visibles de Empresas' }
  },

  {
    path: 'register-admin-s',
    loadChildren: () => import('./register-admin-s/register-admin-s.module').then(m => m.RegisterAdminSPageModule),
    data: { breadcrumb: 'Registrar Empresas' }
  },

  {
    path: 'users-settings',
    loadChildren: () => import('./users-settings/users-settings.module').then(m => m.UsersSettingsPageModule),
    data: { breadcrumb: 'Configuración de Usuarios' }
  },
  {
    path: 'users-register',
    loadChildren: () => import('./users-register/users-register.module').then(m => m.UsersRegisterPageModule),
    data: { breadcrumb: 'Registrar Usuarios' }
  },
  {
    path: 'user-edit',
    loadChildren: () => import('./user-edit/user-edit.module').then(m => m.UserEditPageModule),
    data: { breadcrumb: 'Editar Mi Usuario' }
  },
  {
    path: 'registro-modal',
    loadChildren: () => import('./registro-modal/registro-modal.module').then(m => m.RegistroModalPageModule),
    data: { breadcrumb: 'Registro Modal' }
  },
  {
    path: 'employee-view',
    loadChildren: () => import('./employee-view/employee-view.module').then( m => m.EmployeeViewPageModule)
  },
  {
    path: 'employee-appointment',
    loadChildren: () => import('./employee-appointment/employee-appointment.module').then( m => m.EmployeeAppointmentPageModule)
  },
  {
    path: 'register-example',
    loadChildren: () => import('./register-example/register-example.module').then( m => m.RegisterExamplePageModule)
  },

];

@NgModule({
  imports: [
    RouterModule.forRoot(routes, { preloadingStrategy: PreloadAllModules })
  ],
  exports: [RouterModule]
})
export class AppRoutingModule { }
