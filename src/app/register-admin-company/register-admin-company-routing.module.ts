import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { RegisterAdminCompanyPage } from './register-admin-company.page';

const routes: Routes = [
  {
    path: '',
    component: RegisterAdminCompanyPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class RegisterAdminCompanyPageRoutingModule {}
