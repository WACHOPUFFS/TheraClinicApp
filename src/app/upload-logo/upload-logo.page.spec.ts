import { ComponentFixture, TestBed } from '@angular/core/testing';
import { UploadLogoPage } from './upload-logo.page';

describe('UploadLogoPage', () => {
  let component: UploadLogoPage;
  let fixture: ComponentFixture<UploadLogoPage>;

  beforeEach(async(() => {
    fixture = TestBed.createComponent(UploadLogoPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
