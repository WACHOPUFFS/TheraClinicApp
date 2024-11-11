import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { AuthService } from '../auth.service';
import { ToastController, LoadingController } from '@ionic/angular';
import { NavController } from '@ionic/angular';

@Component({
  selector: 'app-upload-logo',
  templateUrl: './upload-logo.page.html',
  styleUrls: ['./upload-logo.page.scss'],
})
export class UploadLogoPage implements OnInit {
  selectedFile: File;
  currentLogo: string;
  previewLogo: string | ArrayBuffer | null = null;

  constructor(
    private http: HttpClient,
    public authService: AuthService,
    private toastController: ToastController,
    private navCtrl: NavController,
    private loadingController: LoadingController // Añadido el LoadingController
  ) {}

  ngOnInit() {
    this.loadCurrentLogo();
  }

  loadCurrentLogo() {
    const companyId = this.authService.selectedId;
    this.http.get(`https://siinad.mx/php/getCompanyLogo.php?companyId=${companyId}`).subscribe((response: any) => {
      this.currentLogo = response.logoUrl; // Ajusta según la estructura de tu respuesta
    });
  }

  onFileSelected(event: any) {
    this.selectedFile = event.target.files[0];

    const reader = new FileReader();
    reader.onload = (e) => {
      this.previewLogo = e.target?.result || null;
    };
    reader.readAsDataURL(this.selectedFile);
  }

  async uploadLogo() {
    const companyId = this.authService.selectedId;

    if (!companyId || !this.selectedFile) {
      this.mostrarToast('Seleccione un archivo para subir.', 'danger');
      return;
    }

    const loading = await this.loadingController.create({
      message: 'Subiendo logo...', // Mensaje mientras carga
    });
    await loading.present();

    const formData = new FormData();
    formData.append('companyId', companyId);
    formData.append('logo', this.selectedFile);

    this.http.post('https://siinad.mx/php/uploadLogo.php', formData).subscribe(
      async (response: any) => {
        await loading.dismiss(); // Oculta el loading después de la respuesta
        if (response.success) {
          await this.mostrarToast(response.message, 'success');
          this.loadCurrentLogo(); // Recargar el logo actual
        } else {
          await this.mostrarToast(response.error, 'danger');
        }
      },
      async (error) => {
        await loading.dismiss(); // Oculta el loading en caso de error
        console.error('Error en la solicitud POST:', error);
        await this.mostrarToast('Error al subir el logo.', 'danger');
      }
    );
  }

  async mostrarToast(message: string, color: string) {
    const toast = await this.toastController.create({
      message: message,
      duration: 3000,
      color: color,
    });
    toast.present();
  }

  goBack() {
    this.navCtrl.back();
  }
}
