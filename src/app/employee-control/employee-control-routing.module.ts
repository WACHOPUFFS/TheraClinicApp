import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { EmployeeControlPage } from './employee-control.page';

const routes: Routes = [
  {
    path: '',
    component: EmployeeControlPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class EmployeeControlPageRoutingModule {}
