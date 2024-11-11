import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { AlertController } from '@ionic/angular';
import { AuthService } from '../auth.service';
import { NavController } from '@ionic/angular';


@Component({
  selector: 'app-user-settings',
  templateUrl: './user-settings.page.html',
  styleUrls: ['./user-settings.page.scss'],
})
export class UserSettingsPage implements OnInit {
  idUser: string;
  fullName: string;
  userName: string;
  userEmail: string;
  userPassword: string;
  confirmPassword: string;
  avatar: string;

  constructor(
    private http: HttpClient,
    private alertController: AlertController,
    private authService: AuthService,
    private navCtrl: NavController
  ) {
    this.idUser = this.authService.userId;
  }

  ngOnInit() {
    this.getUserData();
    this.getUserAvatar();
  }

  getUserData() {
    const url = `https://www.siinad.mx/php/get_user.php?idUser=${this.idUser}`;
    this.http.get(url).subscribe((response: any) => {
      console.log('Get User Response:', response); // Log de la respuesta
      if (response.success) {
        this.fullName = response.data.name;
        this.userName = response.data.username;
        this.userEmail = response.data.email;
      } else {
        this.showAlert('Error', response.message);
      }
    });
  }

  getUserAvatar() {
    const url = `https://www.siinad.mx/php/getUserAvatar.php?userId=${this.idUser}`;
    this.http.get(url).subscribe((response: any) => {
      console.log('Get Avatar Response:', response); // Log de la respuesta
      if (response.avatarUrl) {
        this.avatar = response.avatarUrl;
      } else {
        this.showAlert('Error', response.error);
      }
    });
  }

  changeProfilePicture(event: Event) {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (file) {
      const formData = new FormData();
      formData.append('avatar', file);
      formData.append('userId', this.idUser);

      const url = `https://www.siinad.mx/php/upload_avatar.php`;
      this.http.post(url, formData).subscribe((response: any) => {
        console.log('Upload Avatar Response:', response); // Log de la respuesta
        if (response.success) {
          this.avatar = response.filePath;
          this.showAlert('Success', 'Avatar updated successfully.');
        } else {
          this.showAlert('Error', response.error);
        }
      });
    }
  }

  async saveSettings() {
    if (this.userPassword !== this.confirmPassword) {
      await this.showAlert('Error', 'Passwords do not match.');
      return;
    }

    const data = {
      idUser: this.idUser,
      fullName: this.fullName,
      userName: this.userName,
      userEmail: this.userEmail,
      userPassword: this.userPassword // Enviar solo si la contraseña ha sido cambiada
    };

    const url = `https://www.siinad.mx/php/update_user.php`;
    this.http.post(url, data).subscribe(async (response: any) => {
      console.log('Update User Response:', response); // Log de la respuesta
      await this.showAlert(response.success ? 'Success' : 'Error', response.message);
    });
  }

  async showAlert(header: string, message: string) {
    const alert = await this.alertController.create({
      header,
      message,
      buttons: ['OK']
    });
    await alert.present();
  }

  goBack() {
    this.navCtrl.back();
  }
}
