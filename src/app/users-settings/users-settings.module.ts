import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { UsersSettingsPageRoutingModule } from './users-settings-routing.module';

import { UsersSettingsPage } from './users-settings.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    UsersSettingsPageRoutingModule
  ],
  declarations: [UsersSettingsPage]
})
export class UsersSettingsPageModule {}
