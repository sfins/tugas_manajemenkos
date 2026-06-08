import mysql.connector
from datetime import datetime

# Koneksi db
try:
    db = mysql.connector.connect(
        host="localhost",
        user="root",         
        password="",         
        database="tugas"     
    )
    cursor = db.cursor()
except mysql.connector.Error as err:
    print(f"Gagal koneksi ke database: {err}")
    exit()

def menu_kamar():
    while True:
        print("\n--- MANAJEMEN MASTER KAMAR ---")
        print("1. Tampilkan Semua Kamar")
        print("2. Tambah Kamar Baru")
        print("3. Ubah Data Kamar")
        print("4. Hapus Kamar")
        print("0. Kembali ke Menu Utama")
        pilih = input("Pilih Menu (0-4): ")
        
        if pilih == '1':
            cursor.execute("SELECT * FROM kamar")
            res = cursor.fetchall()
            print(f"\n{'ID':<5} {'No Kamar':<10} {'Tipe':<12} {'Harga':<12} {'Status':<10}")
            print("-" * 55)
            for r in res:
                print(f"{r[0]:<5} {r[1]:<10} {r[2]:<12} {r[3]:<12} {r[4]:<10}")
        elif pilih == '2':
            no_kamar = input("Nomor Kamar (ex: C01): ")
            tipe = input("Tipe (Standar/Lengkap): ")
            harga = int(input("Harga Bulanan (Angka): "))
            status = input("Status (Terisi/Kosong): ")
            cursor.execute("INSERT INTO kamar (no_kamar, tipe, harga, status) VALUES (%s, %s, %s, %s)", (no_kamar, tipe, harga, status))
            db.commit()
            print("✓ Kamar sukses ditambahkan!")
        elif pilih == '3':
            id_kamar = int(input("Masukkan ID Kamar yang diubah: "))
            tipe = input("Tipe Baru: ")
            harga = int(input("Harga Baru: "))
            status = input("Status Baru: ")
            cursor.execute("UPDATE kamar SET tipe=%s, harga=%s, status=%s WHERE idkamar=%s", (tipe, harga, status, id_kamar))
            db.commit()
            print("✓ Kamar sukses diperbarui!")
        elif pilih == '4':
            id_kamar = int(input("Masukkan ID Kamar yang dihapus: "))
            cursor.execute("DELETE FROM kamar WHERE idkamar=%s", (id_kamar,))
            db.commit()
            print("✓ Kamar sukses dihapus!")
        elif pilih == '0':
            break


def menu_penghuni():
    while True:
        print("\n--- MANAJEMEN MASTER PENGHUNI ---")
        print("1. Tampilkan Semua Penghuni")
        print("2. Tambah Penghuni Baru")
        print("3. Ubah Data Penghuni")
        print("4. Hapus Penghuni")
        print("0. Kembali ke Menu Utama")
        pilih = input("Pilih Menu (0-4): ")
        
        if pilih == '1':
            query = """
                SELECT p.idpenghuni, p.nama, p.no_hp, p.asal, p.gender, k.no_kamar 
                FROM penghuni p 
                LEFT JOIN kamar k ON p.idkamar = k.idkamar
            """
            cursor.execute(query)
            res = cursor.fetchall()
            print(f"\n{'ID':<5} {'Nama':<12} {'No HP':<15} {'Asal':<12} {'Gender':<12} {'Kamar':<8}")
            print("-" * 68)
            for r in res:
                print(f"{r[0]:<5} {r[1]:<12} {r[2]:<15} {r[3]:<12} {r[4]:<12} {r[5] if r[5] else '-':<8}")
        elif pilih == '2':
            nama = input("Nama Penghuni: ")
            no_hp = input("No HP: ")
            asal = input("Asal Daerah: ")
            gender = input("Gender (Laki-laki/Perempuan): ")
            id_kamar = int(input("Masukkan ID Kamar pilihan: "))
            
            try:
                cursor.execute("INSERT INTO penghuni (nama, no_hp, asal, gender, idkamar) VALUES (%s, %s, %s, %s, %s)", (nama, no_hp, asal, gender, id_kamar))
                # Otomatis update status kamar menjadi Terisi
                cursor.execute("UPDATE kamar SET status='Terisi' WHERE idkamar=%s", (id_kamar,))
                db.commit()
                print("✓ Penghuni sukses didaftarkan & Status Kamar di-update!")
            except mysql.connector.Error as err:
                print(f"Gagal! Pastikan ID Kamar valid. Sepsifikasi Error: {err}")
        elif pilih == '3':
            id_p = int(input("Masukkan ID Penghuni yang diubah: "))
            nama = input("Nama Baru: ")
            no_hp = input("No HP Baru: ")
            asal = input("Asal Baru: ")
            gender = input("Gender Baru: ")
            id_kamar = int(input("ID Kamar Baru: "))
            cursor.execute("UPDATE penghuni SET nama=%s, no_hp=%s, asal=%s, gender=%s, idkamar=%s WHERE idpenghuni=%s", (nama, no_hp, asal, gender, id_kamar, id_p))
            db.commit()
            print("✓ Biodata penghuni diperbarui!")
        elif pilih == '4':
            id_p = int(input("Masukkan ID Penghuni yang dihapus: "))
            cursor.execute("DELETE FROM penghuni WHERE idpenghuni=%s", (id_p,))
            db.commit()
            print("✓ Penghuni sukses dihapus!")
        elif pilih == '0':
            break


def menu_pembayaran():
    while True:
        print("\n--- MANAJEMEN TRANSAKSI PEMBAYARAN ---")
        print("1. Tampilkan Histori Pembayaran")
        print("2. Catat Pembayaran Baru (Bayar Kos)")
        print("3. Ubah Data Transaksi")
        print("4. Hapus Riwayat Transaksi")
        print("0. Kembali ke Menu Utama")
        pilih = input("Pilih Menu (0-4): ")
        
        if pilih == '1':
            query = """
                SELECT b.idpembayaran, p.nama, b.tanggal_bayar, b.jumlah_bayar, b.keterangan 
                FROM pembayaran b
                JOIN penghuni p ON b.idpenghuni = p.idpenghuni
            """
            cursor.execute(query)
            res = cursor.fetchall()
            print(f"\n{'ID Trans':<10} {'Nama Pembayar':<15} {'Tgl Bayar':<12} {'Jumlah':<12} {'Keterangan':<15}")
            print("-" * 70)
            for r in res:
                # Format tampilan
                tgl = r[2].strftime('%d-%m-%Y') if isinstance(r[2], datetime) or hasattr(r[2], 'strftime') else str(r[2])
                print(f"{r[0]:<10} {r[1]:<15} {tgl:<12} {r[3]:<12} {r[4]:<15}")
        elif pilih == '2':
            id_penghuni = int(input("Masukkan ID Penghuni yang membayar: "))
            # Otomatis mencatat tanggal hari ini (Format SQL YYYY-MM-DD)
            tanggal_bayar = datetime.now().strftime('%Y-%m-%d')
            jumlah_bayar = int(input("Jumlah Uang yang Dibayarkan: "))
            keterangan = input("Keterangan (ex: Lunas / Cicil): ")
            
            try:
                cursor.execute("INSERT INTO pembayaran (idpenghuni, tanggal_bayar, jumlah_bayar, keterangan) VALUES (%s, %s, %s, %s)", (id_penghuni, tanggal_bayar, jumlah_bayar, keterangan))
                db.commit()
                print("✓ Transaksi pembayaran berhasil dicatat!")
            except mysql.connector.Error as err:
                print(f"Gagal! Pastikan ID Penghuni terdaftar. Detail Error: {err}")
        elif pilih == '3':
            id_pem = int(input("Masukkan ID Pembayaran yang ingin diubah: "))
            jumlah_bayar = int(input("Jumlah Bayar Baru: "))
            keterangan = input("Keterangan Baru: ")
            cursor.execute("UPDATE pembayaran SET jumlah_bayar=%s, keterangan=%s WHERE idpembayaran=%s", (jumlah_bayar, keterangan, id_pem))
            db.commit()
            print("✓ Data transaksi diubah!")
        elif pilih == '4':
            id_pem = int(input("Masukkan ID Pembayaran yang ingin dihapus: "))
            cursor.execute("DELETE FROM pembayaran WHERE idpembayaran=%s", (id_pem,))
            db.commit()
            print("✓ Riwayat transaksi berhasil dihapus!")
        elif pilih == '0':
            break


def main():
    while True:
        print("\n==========================================")
        print("   SISTEM INFORMASI MANAJEMEN E-KOS CLI   ")
        print("==========================================")
        print("1. Kelola Data Kamar (Master)")
        print("2. Kelola Data Penghuni (Master)")
        print("3. Kelola Transaksi Pembayaran")
        print("4. Keluar Sistem")
        print("==========================================")
        pilihan = input("Pilih Menu Utama (1-4): ")
        
        if pilihan == '1':
            menu_kamar()
        elif pilihan == '2':
            menu_penghuni()
        elif pilihan == '3':
            menu_pembayaran()
        elif pilihan == '4':
            print("\nSistem dimatikan.")
            cursor.close()
            db.close()
            break
        else:
            print("Pilihan menu salah, coba lagi!")

if __name__ == "__main__":
    main()