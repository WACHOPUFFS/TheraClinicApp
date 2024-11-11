import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { EmployeeAppointmentPage } from './employee-appointment.page';

const routes: Routes = [
  {
    path: '',
    component: EmployeeAppointmentPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class EmployeeAppointmentPageRoutingModule {}
