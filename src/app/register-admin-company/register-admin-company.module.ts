import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { RegisterAdminCompanyPageRoutingModule } from './register-admin-company-routing.module';

import { RegisterAdminCompanyPage } from './register-admin-company.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    RegisterAdminCompanyPageRoutingModule
  ],
  declarations: [RegisterAdminCompanyPage]
})
export class RegisterAdminCompanyPageModule {}
