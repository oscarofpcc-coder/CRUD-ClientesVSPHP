using ApiCobranzas.Repositories;

var builder = WebApplication.CreateBuilder(args);





builder.Services.AddControllers();
builder.Services.AddScoped<ClienteRepository>();

builder.Services.AddCors(options =>
{
    options.AddPolicy("AllowAll", p => p
        .AllowAnyOrigin()
        .AllowAnyHeader()
        .AllowAnyMethod());
});






// Add services to the container.
builder.Services.AddControllersWithViews();

var app = builder.Build();

app.UseCors("AllowAll");
app.MapControllers();





// Configure the HTTP request pipeline.
if (!app.Environment.IsDevelopment())
{
    app.UseExceptionHandler("/Home/Error");
}
app.UseRouting();

app.UseAuthorization();

app.MapStaticAssets();

app.MapControllerRoute(
    name: "default",
    pattern: "{controller=Home}/{action=Index}/{id?}")
    .WithStaticAssets();


app.Run();
