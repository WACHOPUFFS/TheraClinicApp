import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { RegisterAdminSPageRoutingModule } from './register-admin-s-routing.module';

import { RegisterAdminSPage } from './register-admin-s.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    RegisterAdminSPageRoutingModule
  ],
  declarations: [RegisterAdminSPage]
})
export class RegisterAdminSPageModule {}
