import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { RegisterExamplePage } from './register-example.page';

const routes: Routes = [
  {
    path: '',
    component: RegisterExamplePage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class RegisterExamplePageRoutingModule {}
