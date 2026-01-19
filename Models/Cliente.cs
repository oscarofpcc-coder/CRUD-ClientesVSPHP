namespace ApiCobranzas.Models
{
    public class Cliente
    {
        public int Id { get; set; }
        public string Cedula { get; set; } = "";
        public string Nombres { get; set; } = "";
        public string? Email { get; set; }
        public string? Telefono { get; set; }
        public string? Direccion { get; set; }
        public bool Estado { get; set; } = true;
        public DateTime Creado_En { get; set; }
    }
}
