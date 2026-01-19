using ApiCobranzas.Models;
using ApiCobranzas.Dtos;
using Dapper;
using MySql.Data.MySqlClient;

namespace ApiCobranzas.Repositories
{
    public class ClienteRepository
    {
        private readonly string _cs;
        public ClienteRepository(IConfiguration config)
        {
            _cs = config.GetConnectionString("MySql")!;
        }

        private MySqlConnection Conn() => new MySqlConnection(_cs);

        public async Task<IEnumerable<Cliente>> GetAllAsync()
        {
            using var db = Conn();
            var sql = "SELECT id, cedula, nombres, email, telefono, direccion, estado, creado_en AS Creado_En FROM clientes ORDER BY id ASC;";
            return await db.QueryAsync<Cliente>(sql);
        }

        public async Task<Cliente?> GetByIdAsync(int id)
        {
            using var db = Conn();
            var sql = "SELECT id, cedula, nombres, email, telefono, direccion, estado, creado_en AS Creado_En FROM clientes WHERE id=@id;";
            return await db.QueryFirstOrDefaultAsync<Cliente>(sql, new { id });
        }

        public async Task<int> CreateAsync(ClienteDto dto)
        {
            using var db = Conn();
            var sql = @"
                INSERT INTO clientes (cedula, nombres, email, telefono, direccion, estado)
                VALUES (@Cedula, @Nombres, @Email, @Telefono, @Direccion, @Estado);
                SELECT LAST_INSERT_ID();
            ";
            return await db.ExecuteScalarAsync<int>(sql, dto);
        }

        public async Task<bool> UpdateAsync(int id, ClienteDto dto)
        {
            using var db = Conn();
            var sql = @"
                UPDATE clientes
                SET cedula=@Cedula, nombres=@Nombres, email=@Email, telefono=@Telefono, direccion=@Direccion, estado=@Estado
                WHERE id=@Id;
            ";
            var rows = await db.ExecuteAsync(sql, new { dto.Cedula, dto.Nombres, dto.Email, dto.Telefono, dto.Direccion, dto.Estado, Id = id });
            return rows > 0;
        }

        public async Task<bool> DeleteAsync(int id)
        {
            using var db = Conn();
            var sql = "DELETE FROM clientes WHERE id=@id;";
            var rows = await db.ExecuteAsync(sql, new { id });
            return rows > 0;
        }
    }
}
