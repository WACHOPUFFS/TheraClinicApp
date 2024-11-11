import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { EmployeeAppointmentPageRoutingModule } from './employee-appointment-routing.module';

import { EmployeeAppointmentPage } from './employee-appointment.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    EmployeeAppointmentPageRoutingModule
  ],
  declarations: [EmployeeAppointmentPage]
})
export class EmployeeAppointmentPageModule {}
