import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { EmployeeControlPageRoutingModule } from './employee-control-routing.module';

import { EmployeeControlPage } from './employee-control.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    EmployeeControlPageRoutingModule
  ],
  declarations: [EmployeeControlPage]
})
export class EmployeeControlPageModule {}
