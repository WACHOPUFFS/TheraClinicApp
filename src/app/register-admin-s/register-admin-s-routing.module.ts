import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { RegisterAdminSPage } from './register-admin-s.page';

const routes: Routes = [
  {
    path: '',
    component: RegisterAdminSPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class RegisterAdminSPageRoutingModule {}
