import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { UsersRegisterPageRoutingModule } from './users-register-routing.module';

import { UsersRegisterPage } from './users-register.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    UsersRegisterPageRoutingModule
  ],
  declarations: [UsersRegisterPage]
})
export class UsersRegisterPageModule {}
